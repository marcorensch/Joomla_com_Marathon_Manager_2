CREATE TABLE IF NOT EXISTS `#__com_marathonmanager_events`
(
    `id`                           int(11)      NOT NULL AUTO_INCREMENT,
    `title`                        varchar(255) NOT NULL,
    `alias`                        varchar(400) NOT NULL DEFAULT '',
    `description`                  text         NOT NULL,
    `earlybird_fee`                float                 DEFAULT NULL,
    `normal_fee`                   float        NOT NULL DEFAULT '0',
    `earlybird_end_date`           DATETIME              DEFAULT NULL,
    `registration_start_date`      DATETIME              DEFAULT NULL,
    `registration_end_date`        DATETIME              DEFAULT NULL,
    `event_date`                   DATETIME              DEFAULT NULL,
    `image`                        varchar(400) NOT NULL DEFAULT '',
    `gallery_content`              text                  default NULL,
    `lastinfos_newsletter_list_id` int(11)               DEFAULT NULL,
    `lat`                          varchar(255) NOT NULL DEFAULT '',
    `lng`                          varchar(255) NOT NULL DEFAULT '',
    `map_option_id`                int(11)               DEFAULT NULL,
    `price_per_map`                float        NOT NULL DEFAULT '0',
    `attachments`                  text                  DEFAULT NULL,
    `result_files`                 text                  DEFAULT NULL,
    `street`                       varchar(255) NOT NULL DEFAULT '',
    `zip`                          varchar(255) NOT NULL DEFAULT '',
    `city`                         varchar(255) NOT NULL DEFAULT '',
    `state`                        tinyint(3)   NOT NULL DEFAULT 0,
    `published`                    tinyint(1)   NOT NULL DEFAULT '1',
    `publish_up`                   DATETIME,
    `publish_down`                 DATETIME,
    `created_by`                   int(11)               DEFAULT NULL,
    `modified_by`                  int(11)               DEFAULT NULL,
    `access`                       int(10) unsigned      DEFAULT NULL,
    `created`                      DATETIME     NOT NULL DEFAULT NOW(),
    `modified`                     DATETIME     NOT NULL DEFAULT NOW(),
    `ordering`                     int(11)      NOT NULL DEFAULT '0',
    `catid`                        int(11)               DEFAULT NULL,
    `arrival_options`              text                  DEFAULT NULL,
    `parcours`                     text                  DEFAULT NULL,
    `privacy_policy_article_id`    int(11)               DEFAULT NULL,

    PRIMARY KEY (`id`),
    KEY `idx_access` (`access`),
    KEY `idx_catid` (`catid`),
    KEY `idx_state` (`published`),
    CONSTRAINT `fk_com_marathonmanager_events_catid` FOREIGN KEY (`catid`) REFERENCES `#__categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_com_marathonmanager_events_access` FOREIGN KEY (`access`) REFERENCES `#__viewlevels` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__com_marathonmanager_arrival_dates`
(
    `id`       int(11)  NOT NULL AUTO_INCREMENT,
    `event_id` int(11)           DEFAULT NULL,
    `date`     DATETIME NOT NULL DEFAULT NOW(),
    `ordering` int(11)  NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_event_id` (`event_id`),
    CONSTRAINT `fk_com_marathonmanager_arrival_dates_event_id` FOREIGN KEY (`event_id`) REFERENCES `#__com_marathonmanager_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__com_marathonmanager_arrival_options`
(
    `id`           int(11)      NOT NULL AUTO_INCREMENT,
    `title`        varchar(255) NOT NULL,
    `icon`         varchar(255)          DEFAULT NULL,
    `alias`        varchar(400) NOT NULL DEFAULT '',
    `catid`        int(11)               DEFAULT NULL,
    `state`        tinyint(3)   NOT NULL DEFAULT 0,
    `published`    tinyint(1)   NOT NULL DEFAULT '1',
    `publish_up`   DATETIME,
    `publish_down` DATETIME,
    `created_by`   int(11)               DEFAULT NULL,
    `modified_by`  int(11)               DEFAULT NULL,
    `access`       int(10) unsigned      DEFAULT NULL,
    `created`      DATETIME     NOT NULL DEFAULT NOW(),
    `modified`     DATETIME     NOT NULL DEFAULT NOW(),
    `ordering`     int(11)      NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_access` (`access`),
    KEY `idx_catid` (`catid`),
    KEY `idx_state` (`published`),
    CONSTRAINT `fk_com_marathonmanager_events_arrival_options_catid` FOREIGN KEY (`catid`) REFERENCES `#__categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_com_marathonmanager_events_arrival_options_access` FOREIGN KEY (`access`) REFERENCES `#__viewlevels` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__com_marathonmanager_courses`
(
    `id`           int(11)      NOT NULL AUTO_INCREMENT,
    `title`        varchar(255) NOT NULL,
    `alias`        varchar(400) NOT NULL DEFAULT '',
    `course_id`    int(11)               DEFAULT NULL,
    `state`        tinyint(3)   NOT NULL DEFAULT 0,
    `published`    tinyint(1)   NOT NULL DEFAULT '1',
    `publish_up`   DATETIME,
    `publish_down` DATETIME,
    `created_by`   int(11)      NOT NULL DEFAULT '0',
    `modified_by`  int(11)      NOT NULL DEFAULT '0',
    `access`       int(10) unsigned      DEFAULT NULL,
    `created`      DATETIME     NOT NULL DEFAULT NOW(),
    `modified`     DATETIME     NOT NULL DEFAULT NOW(),
    `ordering`     int(11)      NOT NULL DEFAULT '0',
    `catid`        int(11)               DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_access` (`access`),
    KEY `idx_state` (`published`),
    CONSTRAINT `fk_com_marathonmanager_parcours_access` FOREIGN KEY (`access`) REFERENCES `#__viewlevels` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__com_marathonmanager_groups`
(
    `id`               int(11)      NOT NULL AUTO_INCREMENT,
    `title`            varchar(255) NOT NULL,
    `alias`            varchar(400) NOT NULL DEFAULT '',
    `group_id`         int(11)               DEFAULT NULL,
    `state`            tinyint(3)   NOT NULL DEFAULT 0,
    `published`        tinyint(1)   NOT NULL DEFAULT '1',
    `publish_up`       DATETIME,
    `publish_down`     DATETIME,
    `created_by`       int(11)      NOT NULL DEFAULT '0',
    `modified_by`      int(11)      NOT NULL DEFAULT '0',
    `access`           int(10) unsigned      DEFAULT NULL,
    `created`          DATETIME     NOT NULL DEFAULT NOW(),
    `modified`         DATETIME     NOT NULL DEFAULT NOW(),
    `ordering`         int(11)      NOT NULL DEFAULT '0',
    `max_participants` int(11)      NOT NULL DEFAULT '1',
    `catid`            int(11)               DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_access` (`access`),
    KEY `idx_state` (`published`),
    CONSTRAINT `fk_com_marathonmanager_groups_access` FOREIGN KEY (`access`) REFERENCES `#__viewlevels` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);



CREATE TABLE IF NOT EXISTS `#__com_marathonmanager_languages`
(
    `id`          int(11)      NOT NULL AUTO_INCREMENT,
    `title`       varchar(255) NOT NULL DEFAULT '',
    `alias`       varchar(400) NOT NULL DEFAULT '',
    `tag`         varchar(255) NOT NULL DEFAULT '',
    `image`       varchar(255) NOT NULL DEFAULT '',
    `published`   tinyint(1)   NOT NULL DEFAULT '1',
    `created_by`  int(11)               DEFAULT NULL,
    `modified_by` int(11)               DEFAULT NULL,
    `access`      int(10) unsigned      DEFAULT NULL,
    `created`     DATETIME     NOT NULL DEFAULT NOW(),
    `modified`    DATETIME     NOT NULL DEFAULT NOW(),
    `ordering`    int(11)      NOT NULL DEFAULT '0',
    `catid`       int(11)               DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_access` (`access`),
    KEY `idx_state` (`published`),
    CONSTRAINT `fk_com_marathonmanager_languages_access` FOREIGN KEY (`access`) REFERENCES `#__viewlevels` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_com_marathonmanager_languages_catid` FOREIGN KEY (`catid`) REFERENCES `#__categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__com_marathonmanager_countries`
(
    `id`          int(11)      NOT NULL AUTO_INCREMENT,
    `title`       varchar(255) NOT NULL DEFAULT '',
    `alias`       varchar(400) NOT NULL DEFAULT '',
    `image`       varchar(255) NOT NULL DEFAULT '',
    `published`   tinyint(1)   NOT NULL DEFAULT '1',
    `created_by`  int(11)               DEFAULT NULL,
    `modified_by` int(11)               DEFAULT NULL,
    `access`      int(10) unsigned      DEFAULT NULL,
    `created`     DATETIME     NOT NULL DEFAULT NOW(),
    `modified`    DATETIME     NOT NULL DEFAULT NOW(),
    `ordering`    int(11)      NOT NULL DEFAULT '0',
    `catid`       int(11)               DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_access` (`access`),
    KEY `idx_state` (`published`),
    CONSTRAINT `fk_com_marathonmanager_countries_access` FOREIGN KEY (`access`) REFERENCES `#__viewlevels` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_com_marathonmanager_countries_catid` FOREIGN KEY (`catid`) REFERENCES `#__categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__com_marathonmanager_registrations`
(
    `id`                int(11)      NOT NULL AUTO_INCREMENT,
    `team_name`         varchar(255) NOT NULL DEFAULT '',
    `alias`             varchar(400) NOT NULL DEFAULT '',
    `event_id`          int(11)               DEFAULT NULL,
    `user_id`           int(11)               DEFAULT NULL,
    `course_id`         int(11)               DEFAULT NULL,
    `group_id`          int(11)               DEFAULT NULL,
    `arrival_option_id` int(11)               DEFAULT NULL,
    `arrival_date`      varchar(255)          DEFAULT NULL,
    `contact_email`     varchar(255) NOT NULL DEFAULT '',
    `contact_phone`     varchar(255) NOT NULL DEFAULT '',
    `maps_count`        int(11)      NOT NULL DEFAULT '0',
    `team_language`     varchar(255) NOT NULL DEFAULT '',
    `participants`      text         NOT NULL,
    `privacy_policy`    tinyint(1)   NOT NULL DEFAULT '0',
    `reference`         varchar(255)          DEFAULT '',
    `payment_status`    tinyint(1)   NOT NULL DEFAULT '0',
    `created_by`        int(11)               DEFAULT '0',
    `modified_by`       int(11)               DEFAULT '0',
    `access`            int(10) unsigned      DEFAULT NULL,
    `created`           DATETIME     NOT NULL DEFAULT NOW(),
    `modified`          DATETIME     NOT NULL DEFAULT NOW(),
    `ordering`          int(11)      NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_access` (`access`),
    KEY `idx_event_id` (`event_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_team_category_id` (`team_category_id`),
    CONSTRAINT `fk_com_marathonmanager_registrations_event_id` FOREIGN KEY (`event_id`) REFERENCES `#__com_marathonmanager_events` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_com_marathonmanager_registrations_user_id` FOREIGN KEY (`user_id`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_com_marathonmanager_registrations_access` FOREIGN KEY (`access`) REFERENCES `#__viewlevels` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);


