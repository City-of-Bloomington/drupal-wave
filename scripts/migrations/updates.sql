alter table users add department_id int unsigned;
alter table users add foreign key (department_id) references departments(id);

update users u
join departments d on u.department=d.name
set u.department_id=d.id;

alter table users drop foreign key users_ibfk_1;
alter table users drop department;
