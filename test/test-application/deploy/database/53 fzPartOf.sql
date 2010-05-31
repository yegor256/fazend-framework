CREATE TABLE fzPartOf (
    id integer not null primary key autoincrement,
    name varchar(255) not null,
    parent integer not null,
    kid integer not null
)
