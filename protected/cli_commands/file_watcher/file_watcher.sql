create table dicom_file_queue(
	id int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	filename varchar(500) NOT NULL,
	detected_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	last_modified_date TIMESTAMP NOT NULL,
	status_id int(10) NOT NULL DEFAULT 0
);

create table dicom_process_status(
	id int(2) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name varchar(255)
);

insert into dicom_process_status (name) VALUES ('new');
insert into dicom_process_status (name) VALUES ('in_progress');
insert into dicom_process_status (name) VALUES ('failed');
insert into dicom_process_status (name) VALUES ('success');

