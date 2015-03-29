# DumbDump

Simple tool for dumping and restoring databases. Dedicated to dev teams, who wants to dump selected and limited databases/tables from production (or backup mirror) database to local developer database

## Instalation

[Download dumbdump.phar](https://github.com/radek-baczynski/dumb-dump/blob/master/build/dumbdump.phar?raw=true)

or

```wget https://github.com/radek-baczynski/dumb-dump/blob/master/build/dumbdump.phar```

and run 

```php dumbdump.phar```


To install globally put dumbdump.phar in /usr/local/bin.

```sh
curl -L -o dumbdump.phar https://github.com/radek-baczynski/dumb-dump/blob/master/build/dumbdump.phar?raw=true
chmod +x dumbdump.phar
sudo mv dumbdump.phar /usr/local/bin/dumbdump
```

Now you can use it just like ```dumbdump```.

## Usage

Create config file, name it with default config.yml name:

```yaml
config:
    source:
        user: source_user
        host: source_database_host
        password: source_password
    destination:
        user: local_user
        host: localhost
        password: local_password

definitions:
    project1_name:
        databases:
            main_base1:
                excludeData: # do not dump datas for this tables
                    - "first_large_table"
                    - "second_large_table"
                    - "..."
            main_base2:
                includeData: # dump data only for this tables
                    - "first_with_data_table_required"
                    - "..."
```

For dump from source
```dumbdump db:dump project1_name```

For restore to destination
```dumbdump db:restore project1_name```

### You can use different configs

```dumbdump db:dump project1_name --config=other.yml```

```dumbdump db:restore project1_name --config=other.yml```
