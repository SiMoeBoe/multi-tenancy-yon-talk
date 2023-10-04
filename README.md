# Multi-Tenancy with Symfony

Hello everyone!

This repository only contains the code from my talk "Multi-Tenancy - Yay or Nay?".

> !!! **It is sample code and must never be used in production as is.** !!!

## How to use this repository

The repository can be used visiting the single branches.
Each branch contains an evolution step from a non-tenant-symfony project to a multi-tenant architecture with one and finally with different databases per tenant.

This readme contains some small explanations to every branch.

At the beginning of each part there are some commands that you should execute to run the code in the specific branch/part if you checked it out.

Please use `docker compose up` to start the project.

## 1: A basic tenant resolver by domain

> Please run \
> `docker compose exec php bin/console doctrine:migrations:migrate` \
> and \
> `docker compose exec php bin/console doctrine:fixtures:load`

If we want a multi tenant architecture, we need a tenant entity, which minimally contains the information that we need to identify a concrete tenant.

In our example, we want to use the *domain* to identify the tenant. This is one of the easiest ways, which helps to hide the multi tenancy from the users.

> Maybe you want to use another identifier, e.g. the name (with maybe a dropdown at login) or a language key or something else.
>
> The technique is almost the same.

Next to some simple changes, this branch introduce two important classes:

### `TenantManager`

The `TenantManager` is a class, which helps us to get the current tenant everytime we need it.

The class is similar to a value object, containing only the current tenant and a corresponding getter and setter, but technically it is a service, that can inject in every other service, where we need the current tenant.

To be sure that we *have* a current tenant when we need it, we do not allow an empty value. If you want to get the current tenant before it is initialized, you will get an exception.

To set the property as early as possible, we need a second class.

### `TenantResolver`

The `TenantResolver` should set the current tenant.

If a request is made to our application, we catch it with a Request-Event-Listener, get the current `HttpHost` (or whatever you use to divide the tenants) and set the current tenant in our `TenantManager`.

### Results of this branch

Now the first step is done. We can identify the current tenant at every place in our application :-)

You can open one of the following domains (defined at the .env file):

* [tenant1.localhost](tenant1.localhost)
* [tenant2.localhost](tenant2.localhost)
* [tenant3.localhost](tenant3.localhost)

Please log in as `firstuser@test.local` with password `password` (see `UserFixtures`).

You get the current tenant at the dashboard.

## 2: Add tenant relevant entity

> Please run \
> `docker compose exec php composer i` \
> and \
> `docker compose exec php bin/console doctrine:migrations:migrate`

In our current environment we use only one database for all tenants.

To show how we deal with tenant specific data, we introduce a new entity `BlogPost`.
The entity stores some data like an entry for a blog.

The `BlogPost` entity has a property named `tenant`, which stores the tenant relation.

At the moment, there are some problems to solve: We can add a new BlogPost with tenant relation, but all BlogPosts will be listed by all tenants.

This will be solved at the next step.

## 3: Automatically filter content by tenant

To be sure to get only the content that relates to a specific tenant, we must add a `where` clause to every database query.
This can be implemented for every repository, for every single query, but when doing this by hand, it is easy to forget one.

Instead of adding the clause to every query by hand, we can add a doctrine filter that will do this for us.

For this, we need some changes. First we introduce a trait named `IsTenantSpecificEntity` and move the tenant-specific property of the `BlogPost` entity and use the trait instead.

The trait can be used in our `TenantAwareFilter` class to identify all entities that need the `where` clause, which then is added in the `TenantAwareFilter`.

Now we add the filter to the doctrine configuration and use the `TenantManager` to set the correct parameter.

Finally, we get only tenant specific blog entries if we reload the page.

## 4: Tenant aware commands

We identify tenants by domain and this works very well for all requests.
But we want to use symfony commands, too!

We introduce a `ListPostsCommand`, which should list all existing blog posts per tenant.

If we run this command, it fails, because have not set the current command. For this, we want to introduce an option `--tenant`.

This option can be used inside the `TenantResolver` to get the current tenant. We listen to the `ConsoleCommandEvent` and set the current tenant the same way as by [requests](#tenantresolver).

We *can* implement this `--tenant` option for every command, e.g. with an abstract command. But if we do this, all commands from symfony itself or other third party packages will not work.

To solve this, we not only use the listener to set the current tenant, but to introduce this option as well.

To prevent problems that can occur when not setting the tenant, we extend our `TenantAwareFilter` with a check, if the tenant is set.

Now every command, including all existing commands, have the `--tenant` option.

> Please note: Since we set the option in the listener first, you won't see it in the help dialogue of a command. \
> If you want this, you can use an abstract command or trait for your own commands to add the option, or \
> if you want to have it for all existing commands, you must implement a compiler pass to add the option for every command.

## 5: Deny access if a user is not part of the tenant

> Please run \
> `docker compose exec php bin/console doctrine:migrations:migrate` \
> and \
> `docker compose exec php bin/console doctrine:fixtures:load`

Now, our multi tenant architecture with one database works well, but we still have some issues.

At the moment, any user can access any tenant!

To have tenant aware users, we introduce a new relationship between our `Tenant` and `User` entity. This collection contains all users that should have access to a tenant.
The important step is to check if a logged-in user has access to the tenant that he wants to access.

For commands nothing changes. Commands are only executable by an admin via CLI.

For requests, we extend our `TenantResolver` to check if the current tenant user collection contains the current user. If that is the case: we do nothing, otherwise, the user will be logged out.

## 6: Allow users to switch between tenants

> Please run \
> `docker compose down && docker compose up` \
> and \
> `docker compose exec php bin/console doctrine:fixtures:load`

If we have an application where it is okay for a user to know that it is a multi tenant application, we might want a user to be able to switch to another tenant without new login.

Of course, we can use single sign-on, but if we have subdomains, there is an easier way:

We can add a `cookie_domain` to the session settings at `config/packages/framework.yaml` with our main domain. Now, if a user is logged in at a tenant on a subdomain and switches to another tenant with another subdomain, the user is still logged-in.

> This only works for subdomains! \
> Alternatively, you can use another identifier (see [1: A basic tenant resolver by domain](#1-a-basic-tenant-resolver-by-domain)) \
> or use single sign-on

## 7: Split database between tenant config and tenants content

> Please run \
> `docker compose down -v && docker compose up` \
> and \
> `docker compose exec php bin/console doctrine:migrations:migrate --configuration=migrations/landlord.php` \
> and \
> `docker compose exec php bin/console doctrine:migrations:migrate --configuration=migrations/tenant.php` \
> and \
> `docker compose exec php bin/console doctrine:fixtures:load --em landlord`

To prepare our test application for these needs, we have to make some changes.
This branch prepares the code by splitting the database into two different databases -
one for the tenant configuration, one for the tenant content.

> This is only one step to a multi database based tenant architecture, the finished implementation is in the next part

First, we introduce a new wording: Landlord. The landlord database contains all entities that we need for *all* tenants. In our example, this will be the `Tenant` entity and the `User` entity.
The `BlogPost` entity is stored in the tenant database.

To start with, we have to add the landlord database as a second connection that is used by a new landlord entity manager to our `config/packages/doctrine.yaml`.
The tenant database is still our default connection and is used by the default entity manager.

We split the entity folder in two sub-folders for landlord and tenants as well as the migrations.

For each migration sub-folder we add a configuration file with the correct connection and other settings.
These configurations must be added as an option to doctrine migration commands.

In the `config/packages/security.yaml` we have to add the correct `manager_name` for the `app_user_provider` and change the entity class path (as well as in all other classes which imports entities).

We must change the tenant property in our `IsTenantSpecificEntity` trait to store only the tenant id as integer without the property knowing it is a tenant relation, because the `Tenant` entity is stored at the landlord database.
In the next step we will remove this trait completely, but at the moment we still need it.

In the same way we must change the `BlogPostRepository` to set the tenant id instead of the tenant subject.

Now, everything should run as before, but we use different databases for landlord data and tenant related data.

> There are many discussions, which data belongs to the landlord database and which to the tenant database.
>
> To decide, you should think about which data is needed for all tenants (everytime),
> e.g. for tenant identifying and tenant configurations, as well as translations (if you store them in the database) and equal data.
>
> The user *can* be in the landlord (like our example), if the application does not store (a lot of) data that belongs to a special user. \
> For a blog system like the example normally you want to store the user at the tenant database to be able to add relations between e.g. a blog post and the user that creates this post.
>
> At the other side, the user tenant switch will be very complex (without SSO), if every tenant stores its own user table. For this reason, we leave the user at the landlord in this example project.

## 8: Use different databases per tenant

> Please run \
> `docker compose down -v && docker compose up` \
> and \
> `docker compose exec php bin/console doctrine:migrations:migrate --configuration=migrations/landlord.php` \
> and \
> `docker compose exec php bin/console doctrine:fixtures:load --em landlord` \
> and for every tenant (see `TenantFixtures`): \
> `docker compose exec php bin/console doctrine:migrations:migrate --configuration=migrations/tenant.php --tenant [tenant domain]`

A separate landlord database but only one database for all tenants is not useful, only complex. We want to have a single database for each tenant to get all positive effects:

* easier backups per tenant
* no `where` clause for tenant specific data (because the tenant data is unique per tenant database)
* using third party without headache
* ...

For this, we have to manipulate the database connection for tenants (our default connection) to change the database connection data if we change the tenant.
In this example we only change the database name per tenant, the database credentials do not change.
But if you want to use different credentials for each tenant, the technique is the same.

First, we remove the database name from the `DATABASE_TENANT_URL` at the `.env` file.
We store this name at the `Tenant` entity and will set it later.

Before we implement the database switch, we can remove some classes which will not be need in the future:

The `IsTenantSpecificEntity` trait will no longer be needed, because each tenant database is completely separated from the other tenants.
At the same time we can remove the `TenantAwareFilter` which uses the traits data.
Don't forget to remove the setting of the tenant id parameter at the `BlogPostRepository` and remove the filter from the `config/package/doctrine.yaml` as well as the usage of the filter at the `TenantManager`.

We have two ways to implement the database connection switch. For both ways, there is example code in this repository.

### Switch connection using doctrine middleware

We can use [doctrine middlewares](https://www.doctrine-project.org/projects/doctrine-bundle/en/latest/middlewares.html) to manipulate the connection.

For this, we add a `SwitchTenantDatabaseConnectionDriver` which changes the database name to the current tenant.

We can activate this driver using the `SwitchTenantDatabaseMiddleware`.

> This solution works very well until we try some code that needs the database name before running the doctrine connection middleware.
>
> Especially the `doctrine:database:create` command fails.
>
> If you do not need this command, e.g. an administrator prepares all databases before using your app, this solution is the best in my opinion.
> Otherwise, check out the next solution, which we will use in this project.

### Switch connection using a connection wrapper

To avoid unexpected behaviour with code, that needs the database name before establishing a database connection (see doctrine middleware solution), we want to use our own connection class.

For this, we extend the `Doctrine\DBAL\Connection` and add a new function `selectDatabase()`, which gets the current tenant as parameter.
The function closes open connections and reestablishes a connection with the current needed database name.

You can see this code at the `TenantDatabaseConnectionWrapper` class.

This wrapper must be activated at the default connection at `config/packages/doctrine.yaml` by adding the key `wrapper_class` with our own wrapper.

Last, we use the `selectDatabase` function if we set the current tenant at our `TenantManager`.

## Final thoughts

This repository was created during the preparation of a talk.
The code is only an example how we can build a multi tenancy application.

One question is: why not build a multi tenancy package?

In my opinion each application is a little bit different and needs another way of implementing the multi tenancy.

For example, which identifier did we use? Should we implement one or more databases? Are all databases created before deploying? Should the user be part of tenant or landlord? Which third party packages do I use?

This example code should show you how easy it is to implement multi tenancy in symfony generally. I think, it is so easy, that we can implement it new for each application. We need the understanding of how multi tenancy works instead of a ready to use package with lots of configuration for all possible edge cases.

I hope, this repository shows you a way and enables you to build your own fantastic multi tenancy app.
