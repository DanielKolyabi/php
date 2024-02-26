create table myApp.users
(
    id         int          not null auto_increment,
    username   varchar(64)  not null,
    password   varchar(64)  null,
    auth_hash  varchar(128) null,
    email      varchar(64)  not null,
    birthday   datetime     null,
    created_at datetime     not null default now(),
    primary key (id)
)
    engine = InnoDB
    default character set utf8;