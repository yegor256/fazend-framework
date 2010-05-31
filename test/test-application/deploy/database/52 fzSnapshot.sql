CREATE TABLE fzSnapshot (
    id integer not null primary key autoincrement,
    fzObject integer not null,
    properties varchar(1024),
    version integer not null,
    alive integer not null,
    updated timestamp,
    user integer,
    comment varchar(255),
    baselined boolean not null
) 
