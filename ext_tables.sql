CREATE TABLE tx_tracking_pageview (
    url text,
    user_agent text,
    type int(11) unsigned DEFAULT '0' NOT NULL,
);
