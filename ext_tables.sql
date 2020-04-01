CREATE TABLE tx_tracking_pageview (
    url text,
    user_agent text,
    operating_system varchar(255) DEFAULT '' NOT NULL,
    type int(11) unsigned DEFAULT '0' NOT NULL,
);
