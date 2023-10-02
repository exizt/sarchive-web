/*
 Navicat Premium Data Transfer

 Source Server         : ASV
 Source Server Type    : MySQL
 Source Server Version : 50729
 Source Host           : 211.110.229.116:3306
 Source Schema         : SERV_SARCHIVE

 Target Server Type    : MySQL
 Target Server Version : 50729
 File Encoding         : 65001

 Date: 08/09/2021 22:58:44
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


-- ----------------------------
-- Procedure structure for procedure_insert_menus
-- ----------------------------
DROP PROCEDURE IF EXISTS `procedure_insert_menus`;
delimiter ;;
CREATE PROCEDURE `procedure_insert_menus`()
BEGIN

	
	DECLARE currentNodeId, currentParentId  INT;
  DECLARE currentLeft                 INT;
  DECLARE startId                     INT DEFAULT 1;


	
	SET max_heap_table_size = 1024 * 1024 * 512;

	START TRANSACTION;


	
	CREATE TABLE IF NOT EXISTS `sa_board_tree` (
		`lft`     int(10) UNSIGNED 			NOT NULL DEFAULT '1',
		`rgt`     int(10) UNSIGNED 			DEFAULT NULL,
		`category_id` int(10) UNSIGNED 	NOT NULL DEFAULT '0',
		PRIMARY KEY (`lft`)
	) ENGINE=InnoDB
	DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;


	
	
	DROP TABLE IF EXISTS `tmp_tree`;

	
	CREATE TABLE `tmp_tree` (
		`id`			int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`node_id`			int(10) UNSIGNED NOT NULL DEFAULT '0',
		`parent_id` 	int(10) UNSIGNED			DEFAULT NULL,
		`lft`     int(10) UNSIGNED 					DEFAULT NULL,
		`rgt`     int(10) UNSIGNED 					DEFAULT NULL,
		PRIMARY KEY      (`id`),
		INDEX USING HASH (`node_id`),
		INDEX USING HASH (`parent_id`),
		INDEX USING HASH (`lft`),
		INDEX USING HASH (`rgt`)
	) ENGINE = MEMORY

	SELECT 
     null as `id`,
     `id` as `node_id`,
     `parent_id`,
		 null as `lft`,
     null as `rgt`
	FROM
		`sa_boards`
  order by `parent_id` asc, `index` asc;


	
	WHILE EXISTS (SELECT * FROM `tmp_tree` WHERE `parent_id` = 0 AND `lft` IS NULL AND `rgt` IS NULL LIMIT 1) DO
		UPDATE 		`tmp_tree`
			SET 		`lft`  = startId,
							`rgt`  = startId + 1
			WHERE		`parent_id` = 0
				AND		`lft`  IS NULL
				AND		`rgt`  IS NULL
			ORDER BY `id` ASC
			LIMIT  1;

			SET startId = startId + 2;

	END WHILE;


	
	WHILE EXISTS (SELECT * FROM `tmp_tree` WHERE `lft` IS NULL LIMIT 1) DO
		
		SELECT     `tmp_tree`.`node_id`, `tmp_tree`.`parent_id`
			INTO     currentNodeId, currentParentId
		FROM       `tmp_tree`
		INNER JOIN `tmp_tree` AS `parents`
						ON `tmp_tree`.`parent_id` = `parents`.`node_id`
		WHERE      `tmp_tree`.`lft` IS NULL
			AND      `parents`.`lft`  IS NOT NULL
		ORDER BY `tmp_tree`.`id` DESC 
		LIMIT      1;

		
		SELECT  `lft`
			INTO  currentLeft
		FROM    `tmp_tree`
		WHERE   `node_id` = currentParentId;

		
		UPDATE `tmp_tree`
		SET    `rgt` = `rgt` + 2
		WHERE  `rgt` > currentLeft;


		UPDATE `tmp_tree`
		SET    `lft` = `lft` + 2
		WHERE  `lft` > currentLeft;


		
		UPDATE `tmp_tree`
		SET    `lft`  = currentLeft + 1,
					 `rgt` = currentLeft + 2
		WHERE  `node_id`   = currentNodeId;


	END WHILE;


    
    
    
    
    

	TRUNCATE `sa_board_tree`;

	INSERT INTO `sa_board_tree`
	SELECT `lft`,`rgt`,`node_id`
	FROM 	`tmp_tree`
	ORDER BY `lft` asc;


	COMMIT;


  DROP TABLE `tmp_tree`;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for proc_build_folders_systempath_all
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_build_folders_systempath_all`;
delimiter ;;
CREATE PROCEDURE `proc_build_folders_systempath_all`()
BEGIN
	
	DECLARE currentNodeId, currentParentId INT;
	
	SET max_heap_table_size = 1024 * 1024 * 512;

	START TRANSACTION;
	
	

	# tmp_folder 임시 테이블을 생성함
	DROP TABLE IF EXISTS `tmp_folders`;
	CREATE TABLE `tmp_folders` (
		`id`			int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`node_id`			int(10) UNSIGNED NOT NULL DEFAULT '0',
		`parent_id` 	int(10) UNSIGNED			DEFAULT NULL,
		`system_path` varchar(100) DEFAULT NULL,
		`name`				varchar(255) DEFAULT NULL,
		`depth` 			int(10) UNSIGNED DEFAULT NULL,
		`is_completed`		int(10) UNSIGNED DEFAULT NULL,
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
	

	# 최상위 노드에 대한 system_path 업데이트.
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

	# sa_folders 에 반영.
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
-- Procedure structure for proc_build_folders_systempath_all_20210123
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_build_folders_systempath_all_20210123`;
delimiter ;;
CREATE PROCEDURE `proc_build_folders_systempath_all_20210123`()
BEGIN
	
	DECLARE currentNodeId, currentParentId INT;
	
	SET max_heap_table_size = 1024 * 1024 * 512;

	START TRANSACTION;
	
	

	# tmp_folder 임시 테이블을 생성함
	DROP TABLE IF EXISTS `tmp_folders`;
	CREATE TABLE `tmp_folders` (
		`id`			int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`node_id`			int(10) UNSIGNED NOT NULL DEFAULT '0',
		`parent_id` 	int(10) UNSIGNED			DEFAULT NULL,
		`system_path` varchar(100) DEFAULT NULL,
		`name`				varchar(255) DEFAULT NULL,
		`depth` 			int(10) UNSIGNED DEFAULT NULL,
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
		 `depth` as `depth`
	FROM
		`sa_folders`
  order by `depth` asc, `parent_id` asc, `index` asc;
	

	# 최상위 노드에 대한 system_path 업데이트.
	UPDATE `tmp_folders`
	SET    `system_path` = concat('/',`node_id`,'/')
	WHERE  `parent_id` = 0;
	
	
	WHILE EXISTS (SELECT * FROM `tmp_folders` WHERE `system_path` IS NULL LIMIT 1) DO

		SELECT  `node_id`, `parent_id`
			INTO  currentNodeId, currentParentId
		FROM    `tmp_folders`
		WHERE   `system_path` is null 
		limit 1;
		

		# 최상위 루트는 위에서 처리했으므로 남은 것에 대해서만 update
		IF currentParentId != 0 THEN
			
			UPDATE `tmp_folders` as c
			inner join (
				select system_path, node_id
				from tmp_folders as p
			) as parent on parent.node_id = c.parent_id
			set c.system_path = concat(parent.system_path, c.node_id, '/')
			where c.node_id = currentNodeId;
			
		END IF;
		
	END WHILE;

	# sa_folders 에 반영.
	UPDATE `sa_folders` as c
	inner join (
		select system_path, node_id
		from tmp_folders as p
	) as tmp on tmp.node_id = c.id
	set c.system_path = tmp.system_path;

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
	
	DECLARE currentNodeId, currentParentId INT;
	
	SET max_heap_table_size = 1024 * 1024 * 512;

	START TRANSACTION;
	
	

	# tmp_folder 임시 테이블을 생성함
	DROP TABLE IF EXISTS `tmp_folders`;
	CREATE TABLE `tmp_folders` (
		`id`			int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`node_id`			int(10) UNSIGNED NOT NULL DEFAULT '0',
		`parent_id` 	int(10) UNSIGNED			DEFAULT NULL,
		`system_path` varchar(100) DEFAULT NULL,
		`name`				varchar(255) DEFAULT NULL,
		`depth` 			int(10) UNSIGNED DEFAULT NULL,
		`is_completed`		int(10) UNSIGNED DEFAULT NULL,
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
	

	# 최상위 노드에 대한 system_path 업데이트.
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

	# sa_folders 에 반영.
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
-- Procedure structure for proc_build_folders_systempath_in_archive_20210123
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_build_folders_systempath_in_archive_20210123`;
delimiter ;;
CREATE PROCEDURE `proc_build_folders_systempath_in_archive_20210123`(IN `archive_id` bigint)
BEGIN
	
	DECLARE currentNodeId, currentParentId INT;
	
	SET max_heap_table_size = 1024 * 1024 * 512;

	START TRANSACTION;
	
	

	# tmp_folder 임시 테이블을 생성함
	DROP TABLE IF EXISTS `tmp_folders`;
	CREATE TABLE `tmp_folders` (
		`id`			int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`node_id`			int(10) UNSIGNED NOT NULL DEFAULT '0',
		`parent_id` 	int(10) UNSIGNED			DEFAULT NULL,
		`system_path` varchar(100) DEFAULT NULL,
		`name`				varchar(255) DEFAULT NULL,
		`depth` 			int(10) UNSIGNED DEFAULT NULL,
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
		 `depth` as `depth`
	FROM
		`sa_folders`
	where `sa_folders`.`archive_id` = archive_id
  order by `depth` asc, `parent_id` asc, `index` asc;
	

	# 최상위 노드에 대한 system_path 업데이트.
	UPDATE `tmp_folders`
	SET    `system_path` = concat('/',`node_id`,'/')
	WHERE  `parent_id` = 0;
	
	
	WHILE EXISTS (SELECT * FROM `tmp_folders` WHERE `system_path` IS NULL LIMIT 1) DO

		SELECT  `node_id`, `parent_id`
			INTO  currentNodeId, currentParentId
		FROM    `tmp_folders`
		WHERE   `system_path` is null 
		limit 1;
		

		# 최상위 루트는 위에서 처리했으므로 남은 것에 대해서만 update
		IF currentParentId != 0 THEN
			
			UPDATE `tmp_folders` as c
			inner join (
				select system_path, node_id
				from tmp_folders as p
			) as parent on parent.node_id = c.parent_id
			set c.system_path = concat(parent.system_path, c.node_id, '/')
			where c.node_id = currentNodeId;
			
		END IF;
		
	END WHILE;

	# sa_folders 에 반영.
	UPDATE `sa_folders` as c
	inner join (
		select system_path, node_id
		from tmp_folders as p
	) as tmp on tmp.node_id = c.id
	set c.system_path = tmp.system_path;

	COMMIT;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
