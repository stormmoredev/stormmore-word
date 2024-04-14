create table users
(
    id           integer generated always as identity (minvalue 77)
        constraint users_pk primary key,
    name         varchar(32)                            not null UNIQUE,
    first_name   varchar(32)                            null,
    last_name    varchar(32)                            null,
    role         varchar(32)                            not null,
    email        varchar(256)                           not null UNIQUE,
    password     char(64)                               null,
    is_activated bool                     DEFAULT false not null,
    created_at   timestamp with time zone DEFAULT now() not null,
    updated_at   timestamp with time zone DEFAULT now() not null
);

create table users_tokens
(
    key        UUID                     DEFAULT gen_random_uuid() primary key,
    user_id    integer                                not null
        constraint users_codes_users__fk references users (id),
    valid_to   timestamp with time zone               not null,
    created_at timestamp with time zone DEFAULT now() not null
);

create table claims
(
    user_id integer     not null
        constraint claims_users__fk references users (id),
    name    varchar(16) not null
);

create table sessions
(
    id               UUID                     DEFAULT gen_random_uuid() primary key,
    user_id          integer                                not null
        constraint sessions_users__fk
            references users (id),
    remember         bool                     DEFAULT false not null,
    valid_to         timestamp with time zone               not null,
    created_at       timestamp with time zone DEFAULT now() not null,
    last_activity_at timestamp with time zone DEFAULT now() not null
);

create table articles
(
    id           integer generated always as identity
        constraint articles_pk primary key,
    author_id    integer                                not null
        constraint articles_users__fk references users (id),
    title        varchar(256)                           not null,
    content      text                                   not null,
    language     varchar(8)                             not null,
    opened       int                      DEFAULT 0     not null,
    is_deleted   bool                     DEFAULT false not null,
    is_published bool                     DEFAULT false not null,
    published_at timestamp with time zone,
    created_at   timestamp with time zone DEFAULT now() not null,
    updated_at   timestamp with time zone DEFAULT now() not null
);

create table replies
(
    id         integer generated always as identity
        constraint replies_pk primary key,
    author_id  integer                                not null
        constraint replies_users__fk references users (id),
    article_id integer                                not null
        constraint replies_articles__fk references articles (id),
    down_vote   int default 0                          not null,
    up_vote     int default 0                          not null,
    content     text                                   not null,
    is_approved   bool                   DEFAULT false not null,
    is_reported_as_abuse  bool           DEFAULT false not null,
    is_deleted bool                      DEFAULT false not null,
    created_at timestamp with time zone  DEFAULT now() not null,
    updated_at timestamp with time zone  DEFAULT now() not null
);

create table replies_votes
(
    user_id  integer                                not null
        constraint replies_votes_users__fk references users (id),
    reply_id integer                                not null
        constraint replies_votes_reply__fk references replies (id),

    primary key (user_id, reply_id)
);


INSERT INTO users (name, first_name, last_name, role, email, is_activated, password)
VALUES ('Admin', 'Admin', 'Admin', 'administrator', 'admin@admin.com', true,
        '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918');
INSERT INTO users (name, first_name, last_name, role, email, is_activated, password)
VALUES ('editor', 'Editor', 'Editor', 'editor', 'editor@editor.com', true,
        '1553cc62ff246044c683a61e203e65541990e7fcd4af9443d22b9557ecc9ac54');

INSERT INTO public.articles (author_id, title, content, language, opened, is_deleted, is_published, published_at,
                             created_at, updated_at)
VALUES (77, 'Thank you for checking out Stormmore word.', '<h3 data-placeholder="Article title" class="">Thank you for checking out Stormmore word.</h3><p data-placeholder="Write your article here..." class="">It''s on very early development stage, list of features is long, plans are big and there can be some bugs. So far it works for me as testing platform for my framework. My goal is to do it as good as it can be and in my opinion the best solution for that is write software with it and make changes depends on real scenarios, in this case "Stormmore word". That way framework will be like&nbsp;surgical knife. In first place real life requiments and then make right tools -it''s like sharping tool. There is similar technique called TDD but i''m implementing it to develop framework instead features.&nbsp;</p><p data-placeholder="Write your article here..." class="">Don''t hesitate to ask my antyhing.</p>', 'en', 0, false, false, null, '2024-04-08 07:55:58.568895 +00:00', '2024-04-08 08:42:47.000000 +00:00');
INSERT INTO public.articles (author_id, title, content, language, opened, is_deleted, is_published, published_at,
                             created_at, updated_at)
VALUES (77, 'How to start ?', '<h3 data-placeholder="Article title" class="">How to start ?</h3><p data-placeholder="Write your article here..." class="">To sign in and add more entries go to <a href="/admin/signin">/admin/signin</a> and use pre-defined adminstrator account Admin:admin.</p>', 'en', 0, false, false, null, '2024-04-08 08:30:08.582123 +00:00', '2024-04-08 08:50:20.000000 +00:00');



