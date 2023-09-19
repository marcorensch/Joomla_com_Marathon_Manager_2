CREATE TABLE IF NOT EXISTS `#__com_marathonmanager_map_options`
(
    `id`          int(11)      NOT NULL AUTO_INCREMENT,
    `title`       varchar(255) NOT NULL,
    `alias`       varchar(400) NOT NULL DEFAULT '',
    `description` text                  DEFAULT NULL,
    `created_by`  int(11)      NOT NULL DEFAULT '0',
    `modified_by` int(11)      NOT NULL DEFAULT '0',
    `created`     DATETIME     NOT NULL DEFAULT NOW(),
    `modified`    DATETIME     NOT NULL DEFAULT NOW(),
    `ordering`    int(11)      NOT NULL DEFAULT '0',
    `published`   tinyint(1)   NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`),
    KEY `idx_state` (`published`),
    CONSTRAINT `fk_com_marathonmanager_map_options_created_by` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_com_marathonmanager_map_options_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `#__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__com_marathonmanager_events`
(
    `id`                           int(11)          NOT NULL AUTO_INCREMENT,
    `title`                        varchar(255)     NOT NULL,
    `alias`                        varchar(400)     NOT NULL DEFAULT '',
    `description`                  text             NOT NULL,
    `earlybird_fee`                float            NOT NULL DEFAULT '0',
    `normal_fee`                   float            NOT NULL DEFAULT '0',
    `earlybird_end_date`           DATETIME,
    `registration_start_date`      DATETIME,
    `registration_end_date`        DATETIME,
    `event_date`                   DATETIME,
    `image`                        varchar(400)     NOT NULL DEFAULT '',
    `gallery_content`              text                      default NULL,
    `lastinfos_newsletter_list_id` int(11)                   DEFAULT NULL,
    `lat`                          varchar(255)     NOT NULL DEFAULT '',
    `lng`                          varchar(255)     NOT NULL DEFAULT '',
    `map_option_id`                int(11)          NOT NULL DEFAULT '0',
    `price_per_map`                float            NOT NULL DEFAULT '0',
    `attachments`                  text                      DEFAULT NULL,
    `result_files`                 text                      DEFAULT NULL,
    `street`                       varchar(255)     NOT NULL DEFAULT '',
    `zip`                          varchar(255)     NOT NULL DEFAULT '',
    `city`                         varchar(255)     NOT NULL DEFAULT '',
    `state`                        tinyint(3)       NOT NULL DEFAULT 0,
    `published`                    tinyint(1)       NOT NULL DEFAULT '1',
    `publish_up`                   DATETIME,
    `publish_down`                 DATETIME,
    `created_by`                   int(11)          NOT NULL DEFAULT '0',
    `modified_by`                  int(11)          NOT NULL DEFAULT '0',
    `access`                       int(10) unsigned NOT NULL DEFAULT '0',
    `created`                      DATETIME         NOT NULL DEFAULT NOW(),
    `modified`                     DATETIME         NOT NULL DEFAULT NOW(),
    `ordering`                     int(11)          NOT NULL DEFAULT '0',
    `catid`                        int(11)          NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_access` (`access`),
    KEY `idx_catid` (`catid`),
    KEY `idx_state` (`published`),
    CONSTRAINT `fk_com_marathonmanager_events_catid` FOREIGN KEY (`catid`) REFERENCES `#__categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_com_marathonmanager_events_access` FOREIGN KEY (`access`) REFERENCES `#__viewlevels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_com_marathonmanager_events_map_option_id` FOREIGN KEY (`map_option_id`) REFERENCES `#__com_marathonmanager_map_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_com_marathonmanager_events_created_by` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_com_marathonmanager_events_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `#__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__com_marathonmanager_arrival_options`
(
    `id`           int(11)          NOT NULL AUTO_INCREMENT,
    `title`        varchar(255)     NOT NULL,
    `alias`        varchar(400)     NOT NULL DEFAULT '',
    `state`        tinyint(3)       NOT NULL DEFAULT 0,
    `published`    tinyint(1)       NOT NULL DEFAULT '1',
    `publish_up`   DATETIME,
    `publish_down` DATETIME,
    `created_by`   int(11)          NOT NULL DEFAULT '0',
    `modified_by`  int(11)          NOT NULL DEFAULT '0',
    `access`       int(10) unsigned NOT NULL DEFAULT '0',
    `created`      DATETIME         NOT NULL DEFAULT NOW(),
    `modified`     DATETIME         NOT NULL DEFAULT NOW(),
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__com_marathonmanager_events_arrival_options`
(
    `id`                int(11) NOT NULL AUTO_INCREMENT,
    `event_id`          int(11) NOT NULL DEFAULT '0',
    `arrival_option_id` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_com_marathonmanager_events_arrival_options_event_id` FOREIGN KEY (`event_id`) REFERENCES `#__com_marathonmanager_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_com_marathonmanager_events_arrival_options_arrival_option_id` FOREIGN KEY (`arrival_option_id`) REFERENCES `#__com_marathonmanager_arrival_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)


