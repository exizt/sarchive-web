/*
 Source Server Type    : MariaDB
 Source Server Version : 101105
 
 Date: 09/10/2023 15:35:37
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Procedure structure for proc_build_folders_systempath_all
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_build_folders_systempath_all`;
delimiter ;;
CREATE PROCEDURE `proc_build_folders_systempath_all`()
BEGIN
  /* ========================
   * 명칭 : 전체 모든 폴더의 system_path를 재생성하는 프로시저.
   * 코드명 : proc_build_folders_systempath_all
   * 
   * 사용할 일은 초기화 정도의 경우 빼고는 없음.
   * 'proc_build_folders_systempath_in_archive'가 하나의
   * 아카이브만 재생성하는 것과 다르게, 전체를 한 큐에 재생성한다.
   * ========================= */
    DECLARE currentNodeId, currentParentId INT;
    
    SET max_heap_table_size = 1024 * 1024 * 512;

    START TRANSACTION;
    
    /* tmp_folders 임시 테이블을 생성함 */    
    DROP TABLE IF EXISTS `tmp_folders`;
    CREATE TABLE `tmp_folders` (
        `id`             int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `node_id`        int(10) UNSIGNED NOT NULL DEFAULT '0',
        `parent_id`      int(10) UNSIGNED            DEFAULT NULL,
        `system_path`    varchar(100) DEFAULT NULL,
        `name`           varchar(255) DEFAULT NULL,
        `depth`          int(10) UNSIGNED DEFAULT NULL,
        `is_completed`   int(10) UNSIGNED DEFAULT NULL,
        PRIMARY KEY      (`id`),
        INDEX USING HASH (`node_id`),
        INDEX USING HASH (`parent_id`)
    ) ENGINE = MEMORY
    SELECT 
     null as `id`,
     `id` as `node_id`,
     `parent_id`,
         null as `system_path`,
         `name` as `name`,
         null as `depth`,
         null as `is_completed`
    FROM
        `sa_folders`
  order by `depth` asc, `parent_id` asc, `index` asc;
    

    /* 최상위 노드에 대한 system_path 업데이트 */
    UPDATE `tmp_folders`
    SET    `system_path` = concat('/',`node_id`,'/'),
                 `depth` = 1
    WHERE  `parent_id` = 0;
    
    
    WHILE EXISTS (SELECT * FROM `tmp_folders` WHERE `system_path` IS NULL LIMIT 1) DO

        SELECT  `node_id`
            INTO  currentParentId
        FROM    `tmp_folders`
        WHERE   `system_path` is not null
            and `is_completed` is null
        limit 1;
        

        
        UPDATE `tmp_folders` as c
        inner join (
            select system_path, node_id, depth
            from tmp_folders as p
        ) as parent on parent.node_id = c.parent_id
        set c.system_path = concat(parent.system_path, c.node_id, '/'),
        c.`depth` = parent.depth + 1
        where c.parent_id = currentParentId;
        
        update `tmp_folders`
        set `is_completed` = 1
        where `node_id` = currentParentId;
        
        
    END WHILE;

    /* sa_folders에 최종 반영 */
    UPDATE `sa_folders` as c
    inner join (
        select system_path, node_id, depth
        from tmp_folders as p
    ) as tmp on tmp.node_id = c.id
    set c.system_path = tmp.system_path,
    c.depth = tmp.depth;

    COMMIT;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for proc_build_folders_systempath_in_archive
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_build_folders_systempath_in_archive`;
delimiter ;;
CREATE PROCEDURE `proc_build_folders_systempath_in_archive`(IN `archive_id` bigint)
BEGIN
  /* ========================
   * 명칭 : 한 아카이브 내의 폴더의 system_path를 재생성하는 프로시저.
   * 코드명 : proc_build_folders_systempath_in_archive
   * 
   * sa_folders가 반영이 된 상태에서, depth와 system_path를
    * 재계산 및 재생성해주는 프로시저.
   * 트리뷰 등에서 노드의 순서가 변경된 경우, system_path를 아예
    * 새로 만들어주는 것이 깔끔하기 때문. 혹은 하나씩 잘 변경한다거나.
   * ========================= */
    DECLARE currentNodeId, currentParentId INT;
    
    SET max_heap_table_size = 1024 * 1024 * 512;

    START TRANSACTION;
    
    /* STEP 1. 임시 테이블을 생성 */
    /* sa_folders를 가져와서 정렬하고 임시 테이블을 생성한다. */
    DROP TABLE IF EXISTS `tmp_folders`;
    CREATE TABLE `tmp_folders` (
        `id`            int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `node_id`           int(10) UNSIGNED NOT NULL DEFAULT '0',
        `parent_id`     int(10) UNSIGNED            DEFAULT NULL,
        `system_path` varchar(100) DEFAULT NULL,
        `name`              varchar(255) DEFAULT NULL,
        `depth`             int(10) UNSIGNED DEFAULT NULL,
        `is_completed`      int(10) UNSIGNED DEFAULT NULL,
        PRIMARY KEY      (`id`),
        INDEX USING HASH (`node_id`),
        INDEX USING HASH (`parent_id`)
    ) ENGINE = MEMORY
    SELECT 
     null as `id`,
     `id` as `node_id`,
     `parent_id`,
         null as `system_path`,
         `name` as `name`,
         null as `depth`,
         null as `is_completed`
    FROM
        `sa_folders`
    where `sa_folders`.`archive_id` = archive_id
  order by `depth` asc, `parent_id` asc, `index` asc;
    

    /* depth=1의 컬럼에 대해서 system_path를 작성한다. */
    UPDATE `tmp_folders`
    SET    `system_path` = concat('/',`node_id`,'/'),
                 `depth` = 1
    WHERE  `parent_id` = 0;
    
    /* depth=1이 아닌 항목을 탐색하면서 system_path를 작성한다. */
    WHILE EXISTS (SELECT * FROM `tmp_folders` WHERE `system_path` IS NULL LIMIT 1) DO

        SELECT  `node_id`
            INTO  currentParentId
        FROM    `tmp_folders`
        WHERE   `system_path` is not null
            and `is_completed` is null
        limit 1;
        

        
        UPDATE `tmp_folders` as c
        inner join (
            select system_path, node_id, depth
            from tmp_folders as p
        ) as parent on parent.node_id = c.parent_id
        set c.system_path = concat(parent.system_path, c.node_id, '/'),
        c.`depth` = parent.depth + 1
        where c.parent_id = currentParentId;
        
        update `tmp_folders`
        set `is_completed` = 1
        where `node_id` = currentParentId;
        
        
    END WHILE;

    /* 임시로 생성한 systempath와 depth를 적용시킨다. */
    UPDATE `sa_folders` as c
    inner join (
        select system_path, node_id, depth
        from tmp_folders as p
    ) as tmp on tmp.node_id = c.id
    set c.system_path = tmp.system_path,
    c.depth = tmp.depth;

    COMMIT;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
