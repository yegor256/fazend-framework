create table fz_source (
    id integer not null primary key autoincrement, 
    name varchar(255) not null, 
    fz_metric integer not null constraint fz_source_metric references fz_metric(id) on delete cascade on update cascade
);