# Software Engineer Test
## Brief
Develop a combined backend and frontend application that consumes JSON feed and displays a (at minimum, barebones) website with form provided in JSON.
## We expect to see:
1. A Laravel backend application in PHP that consumes the JSON feed, publishes it to a database, and exposes the database via APIs.
2. A database that stores the data in JSON in a well-formed way, with appropriate indexes.
3. Frontend that calls the backend API, shows the Form and submit the form to API.
5. The design of the frontend is not important and is not judged in this test
6. Memory usage should be a consideration

## What would be nice to have:
1. Unit tests
2. Migrations

------------------------------------------------------------------------------

## What i delivered :

- [x] Laravel backend application in PHP that consumes the JSON feed.
- [x] JSON feed is imported and published to a MySQL database through the `forms:import` Artisan command.
- [x] Database stores the original source structures as JSON columns.
- [x] Database uses normalized tables, foreign keys, unique constraints, and query indexes.
- [x] Versioned API exposes the stored form schema.
- [x] Frontend loads the form dynamically from the backend API.
- [x] Frontend submits completed form answers to the backend API.
- [x] Backend validates submitted field types and verifies option ownership.
- [x] Memory-conscious import design uses one source read, database transactions, and idempotent upserts.
- [x] Basic functional frontend is provided; visual design is intentionally minimal.

## Nice to haves:

- [x] Unit and feature tests — run with `./vendor/bin/sail artisan test`
- [x] Database migrations — run with `./vendor/bin/sail artisan migrate`
- [x] Code-style validation — run with `./vendor/bin/sail pint --test`
- [x] GitHub Actions CI runs tests and formatting checks on pushes and pull requests.