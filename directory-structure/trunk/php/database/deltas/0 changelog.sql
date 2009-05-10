CREATE TABLE changelog (
	change_number BIGINT NOT NULL,
	delta_set VARCHAR(10) NOT NULL,
	start_dt INTEGER NOT NULL,
	complete_dt INTEGER NULL,
	applied_by VARCHAR(100) NOT NULL,
	description VARCHAR(500) NOT NULL);

--//@UNDO

DROP TABLE `changelog`;
