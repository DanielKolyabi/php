create table myApp.users
(
    id_user                 int         not null auto_increment,
    user_name               varchar(45) null,
    user_lastname           varchar(45) null,
    user_birthday_timestamp int         null,
    primary key (id_user)
)
    engine = InnoDB
    default character set utf8;