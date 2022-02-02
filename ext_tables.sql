CREATE TABLE tx_tracking_pageview (
    url text,
    user_agent text,
    operating_system varchar(255) DEFAULT '' NOT NULL,
    type int(11) unsigned DEFAULT '0' NOT NULL,

    KEY page_views_per_page (pid,uid,crdate),
    KEY language (l10n_parent,sys_language_uid),
);

CREATE TABLE tx_tracking_recordview (
    url text,
    user_agent text,
    operating_system varchar(255) DEFAULT '' NOT NULL,
    record varchar(255) DEFAULT '' NOT NULL,
    record_uid int(11) unsigned DEFAULT '0' NOT NULL,
    record_table_name varchar(255) DEFAULT '' NOT NULL,

    KEY record_views_per_page (pid,uid,crdate),
    KEY language (l10n_parent,sys_language_uid),
);
