create table product (
    id integer not null primary key autoincrement, 
    text varchar(1024) not null, 
    owner integer not null constraint fk_product_owner references owner(id));
