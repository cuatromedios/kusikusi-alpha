# Kusikusi

### Installation
Kusikusi boilerplate is based in [Lumen Framework](https://lumen.laravel.com/), you should be familiarized with Lumen or Laravel framework first.

> TODO: Using https://getcomposer.org/doc/03-cli.md#create-project

1. First install the dependencies
   ```shell script
   composer install
   ```
2. Rename the `.env.example` file to `.env   
3. Generate an application key. Kusikusi includes [Lumen Generator](https://github.com/flipboxstudio/lumen-generator) so you can run this command to generate a applicatio key
   ```shell script
   php artisan key:generate
   ```
4. Configure the database connection. In your .env file, filling the `DB_*` configuration options, dont forget to set the desired APP_TIMEZONE
5. Run the migrations 
   ```shell script
   php artisan migrate
   ```
1. Serve your app! (you can use `--host` and `--port` options)
   ```shell script
   php artisan serve
   ```

## License

The Kusikusi framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
