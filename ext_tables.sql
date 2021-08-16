CREATE TABLE tx_tracking_pageview (
    url text,
    user_agent text,
    type int(11) unsigned DEFAULT '0' NOT NULL,
    compatible_version varchar(11) DEFAULT 'v1.1.4' NOT NULL,

    KEY page_views_per_page (pid,uid,crdate),
    KEY language (l10n_parent,sys_language_uid),
    KEY compatible_version (compatible_version),
);

CREATE TABLE tx_tracking_recordview (
    url text,
    user_agent text,
    record varchar(255) DEFAULT '' NOT NULL,
    record_uid int(11) unsigned DEFAULT '0' NOT NULL,
    record_table_name varchar(255) DEFAULT '' NOT NULL,
    compatible_version varchar(11) DEFAULT 'v1.1.4' NOT NULL,

    KEY record_views_per_page (pid,uid,crdate),
    KEY language (l10n_parent,sys_language_uid),
    KEY compatible_version (compatible_version),
);

CREATE TABLE tx_tracking_tag (
    record_uid int(11) unsigned DEFAULT '0' NOT NULL,
    record_table_name varchar(255) DEFAULT '' NOT NULL,
    name varchar(255) DEFAULT '' NOT NULL,
    value longtext DEFAULT '' NOT NULL,
    compatible_version varchar(11) DEFAULT 'v2.0.0' NOT NULL,

    KEY combined_identifier (record_uid,record_table_name,name,value),
    KEY compatible_version (compatible_version),
);
