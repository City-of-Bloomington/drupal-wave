create table reports (
    id       int unsigned not null primary key auto_increment,
    nid      int unsigned not null,
    path     varchar(255) not null,
    created  timestamp    not null default CURRENT_TIMESTAMP,
    error    tinyint unsigned,
    contrast tinyint unsigned,
    alert    tinyint unsigned,
    report   json
);
