create table fz_dependency (
    id integer not null primary key autoincrement, 
    kid integer not null constraint kid references fz_metric(id) on delete cascade on update cascade, 
    fz_metric integer not null constraint fz_metric references fz_metric(id) on delete cascade on update cascade
);