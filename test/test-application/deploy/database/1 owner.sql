--
-- @see Model_Owner
--
create table owner (
    id integer not null primary key autoincrement, 
    name varchar(50) not null,
    created date default current_date
)
