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
    photo        varchar(64)                            null,
    about_me     text                                   null,
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
        constraint sessions_users__fk references users (id),
    remember         bool                     DEFAULT false not null,
    valid_to         timestamp with time zone               not null,
    created_at       timestamp with time zone DEFAULT now() not null,
    last_activity_at timestamp with time zone DEFAULT now() not null
);

create table abuse_categories
(
    id   integer generated always as identity
        constraint abuse_categories_pk primary key,
    name varchar(512) not null
);

create table categories
(
    id          integer generated always as identity
        constraint categories_pk primary key,
    parent_id   integer                                null
        constraint categories_categories__fk references categories (id),
    name        varchar(256)                           not null,
    slug        varchar(256)                           not null UNIQUE,
    description varchar(1024)                          null,
    sequence    smallint                 DEFAULT 1     not null,
    type        smallint                 DEFAULT 1     not null,
    is_deleted  bool                     DEFAULT false not null,
    created_at  timestamp with time zone DEFAULT now() not null,
    updated_at  timestamp with time zone DEFAULT now() not null
);

create table entries
(
    id               integer generated always as identity
        constraint entries_pk primary key,
    author_id        integer                                not null
        constraint entries_users__fk references users (id),
    category_id      integer                                null
        constraint entries_categories__fk references categories (id),
    title            varchar(128)                           not null,
    subtitle         varchar(256)                           null,
    slug             varchar(128)                           not null UNIQUE,
    content          text                                   not null,
    titled_media     varchar(512)                               null,
    language         varchar(8)                             not null,
    type             smallint                 DEFAULT 1     not null,
    opens_num        int                      DEFAULT 0     not null,
    votes_num        int                      DEFAULT 0     not null,
    bookmarks_num    int                      DEFAULT 0     not null,
    replies_num      int                      default 0     not null,
    is_deleted       bool                     DEFAULT false not null,
    is_published     bool                     DEFAULT false not null,
    published_at     timestamp with time zone DEFAULT null,
    last_activity_at timestamp with time zone DEFAULT now(),
    created_at       timestamp with time zone DEFAULT now() not null,
    updated_at       timestamp with time zone DEFAULT now() not null
);

create table entry_votes
(
    user_id    integer                                not null
        constraint entries_votes_users__fk references users (id),
    entry_id   integer                                not null
        constraint entries_votes_entries__fk references entries (id),
    created_at timestamp with time zone DEFAULT now() not null,
    UNIQUE (user_id, entry_id)
);

create table entry_bookmarks
(
    user_id  integer not null
        constraint entries_bookmarks_users__fk references users (id),
    entry_id integer not null
        constraint entries_bookmarks_entries__fk references entries (id),
    UNIQUE (user_id, entry_id)
);

create table entry_abuses
(
    id          integer generated always as identity
        constraint entry_abuses_media_pk primary key,
    category_id integer                                not null
        constraint entry_abuses_category__fk references abuse_categories (id),
    content     varchar(512)                           null,
    created_at  timestamp with time zone DEFAULT now() not null
);


create table replies
(
    id                   integer generated always as identity
        constraint replies_pk primary key,
    author_id            integer                                not null
        constraint replies_users__fk references users (id),
    entry_id             integer                                not null
        constraint replies_entries__fk references entries (id),
    down_vote            int                      default 0     not null,
    up_vote              int                      default 0     not null,
    content              text                                   not null,
    is_approved          bool                     DEFAULT false not null,
    is_reported_as_abuse bool                     DEFAULT false not null,
    is_deleted           bool                     DEFAULT false not null,
    created_at           timestamp with time zone DEFAULT now() not null,
    updated_at           timestamp with time zone DEFAULT now() not null
);

create table replies_votes
(
    user_id  integer not null
        constraint replies_votes_users__fk references users (id),
    reply_id integer not null
        constraint replies_votes_reply__fk references replies (id),

    primary key (user_id, reply_id)
);


INSERT INTO users (name, first_name, last_name, role, email, is_activated, password)
VALUES ('Admin', 'Admin', 'Admin', 'administrator', 'admin@admin.com', true,
        '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918');
INSERT INTO users (name, first_name, last_name, role, email, is_activated, password)
VALUES ('editor', 'Editor', 'Editor', 'editor', 'editor@editor.com', true,
        '1553cc62ff246044c683a61e203e65541990e7fcd4af9443d22b9557ecc9ac54');

INSERT INTO entries (author_id,
                     title,
                     subtitle,
                     slug,
                     content,
                     language, is_published, published_at)
VALUES (77,
        'Thank you for checking out Stormmore Community word.',
        'You dont know''t what next ? Read this to learn how grow your community platform.',
        'thank-you-for-checking-stormmore-communityword',
        '<p>To start editing <a href="/signin">sign in</a> with predefined account (email: admin@admin.com, password: admin) .</p>',
        'en', true, now());



