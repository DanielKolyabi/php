# Groups
create table myApp.groups
(
    `id`         int         not null auto_increment,
    `name`       varchar(32) not null,
    `created_at` datetime    null default now(),
    `updated_at` datetime    null default now(),
    primary key (`id`)
)
    engine = InnoDB
    default character set utf8;

create trigger beforeUpdateGroups
    before update
    on myApp.groups
    for each row
begin
    if new.created_at is null then
        set new.created_at = now();
    end if;
    if new.updated_at is null || old.updated_at = new.updated_at then
        set new.updated_at = now();
    end if;
end;

insert into myApp.groups(`name`)
values ('user'),
       ('admin');

# Users
create table myApp.users
(
    `id`         int          not null auto_increment,
    `username`   varchar(64)  not null unique,
    `password`   varchar(254) null,
    `group_id`   int          not null,
    `auth_hash`  varchar(512) null,
    `email`      varchar(64)  not null,
    `birthday`   datetime     null,
    `created_at` datetime     null default now(),
    `updated_at` datetime     null default now(),
    primary key (`id`),
    foreign key (`group_id`) references myApp.groups (`id`) on delete cascade,
    index idx_group_id (`group_id`)
)
    engine = InnoDB
    default character set utf8;

create trigger beforeUpdateUsers
    before update
    on myApp.users
    for each row
begin
    if new.created_at is null then
        set new.created_at = now();
    end if;
    if new.updated_at is null || old.updated_at = new.updated_at then
        set new.updated_at = now();
    end if;
end;

insert into myApp.users(`username`, `password`, `group_id`, `email`)
values ('admin',
        '$2y$10$w2lV/9i9leJTZbXm7nMxVeDuMRqBXl4dereB.sCYIRpCFWg5.3JSO',
        (select `id` from myApp.groups where `name` = 'admin'),
        'admin@example.tt');