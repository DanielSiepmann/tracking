CREATE TABLE tx_tracking_pageview (
    url text,
    user_agent text,
    operating_system varchar(255) DEFAULT '' NOT NULL,
    type int(11) unsigned DEFAULT '0' NOT NULL,
);

CREATE TABLE tx_tracking_recordview (
    url text,
    user_agent text,
    operating_system varchar(255) DEFAULT '' NOT NULL,
    record varchar(255) DEFAULT '' NOT NULL,
    record_uid int(11) unsigned DEFAULT '0' NOT NULL,
    record_table_name varchar(255) DEFAULT '' NOT NULL,
);
