BEGIN

	# 변수 정의 
	DECLARE currentNodeId, currentParentId  INT;
  DECLARE currentLeft                 INT;
  DECLARE startId                     INT DEFAULT 1;


	# 메모리 테이블 힙사이즈
	SET max_heap_table_size = 1024 * 1024 * 512;

	START TRANSACTION;


	# 결과가 최종적으로 저장될 테이블
	CREATE TABLE IF NOT EXISTS `sa_board_tree` (
		`lft`     int(10) UNSIGNED 			NOT NULL DEFAULT '1',
		`rgt`     int(10) UNSIGNED 			DEFAULT NULL,
		`category_id` int(10) UNSIGNED 	NOT NULL DEFAULT '0',
		PRIMARY KEY (`lft`)
	) ENGINE=InnoDB
	DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;


	# Temporary MEMORY table to do all the heavy lifting in,
	# otherwise performance is simply abysmal.
	DROP TABLE IF EXISTS `tmp_tree`;

	# Step 1. 생성 임시 테이블 및 데이터 복사 
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


	# Step 2. 최상위 루트를 찾아서 각각 right, left 값을 셋팅 
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


	# Numbering all child elements
	WHILE EXISTS (SELECT * FROM `tmp_tree` WHERE `lft` IS NULL LIMIT 1) DO
		# Picking an unprocessed element which has a processed parent.
		SELECT     `tmp_tree`.`node_id`, `tmp_tree`.`parent_id`
			INTO     currentNodeId, currentParentId
		FROM       `tmp_tree`
		INNER JOIN `tmp_tree` AS `parents`
						ON `tmp_tree`.`parent_id` = `parents`.`node_id`
		WHERE      `tmp_tree`.`lft` IS NULL
			AND      `parents`.`lft`  IS NOT NULL
		ORDER BY `tmp_tree`.`id` DESC 
		LIMIT      1;

		# Finding the parent's lft value.
		SELECT  `lft`
			INTO  currentLeft
		FROM    `tmp_tree`
		WHERE   `node_id` = currentParentId;

		# Shifting all elements to the right of the current element 2 to the right.
		UPDATE `tmp_tree`
		SET    `rgt` = `rgt` + 2
		WHERE  `rgt` > currentLeft;


		UPDATE `tmp_tree`
		SET    `lft` = `lft` + 2
		WHERE  `lft` > currentLeft;


		# Setting lft and rgt values for current element.
		UPDATE `tmp_tree`
		SET    `lft`  = currentLeft + 1,
					 `rgt` = currentLeft + 2
		WHERE  `node_id`   = currentNodeId;


	END WHILE;


    # Writing calculated values back to physical table.
    #UPDATE `tree`, `tmp_tree`
    #SET    `tree`.`lft`  = `tmp_tree`.`lft`,
    #       `tree`.`rgt` = `tmp_tree`.`rgt`
    #WHERE  `tree`.`id`   = `tmp_tree`.`id`;

	TRUNCATE `sa_board_tree`;

	INSERT INTO `sa_board_tree`
	SELECT `lft`,`rgt`,`node_id`
	FROM 	`tmp_tree`
	ORDER BY `lft` asc;


	COMMIT;


  DROP TABLE `tmp_tree`;
END