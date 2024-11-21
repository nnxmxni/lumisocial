# LUMISOCIAL Laravel Backend

## Requirements

```bash 
- Docker
```

```bash
- Docker compose
```

### Duplicate environments

```bash
cp .env.example .env
```


## Installation

### Install composer packages

```bash
CMD: docker compose run --rm composer install
```


> Don't forget to set your database credentials in the `.env` file

### Run migrations

```bash
CMD: docker compose run --rm artisan migrate --seed
```

### Autoload global function

```bash
CMD: docker compose run --rm composer dump-autoload
```

### RUN instance

```bash
CMD: docker compose build php 
```

```bash
CMD: docker compose up -d 
```


### API access

```url
(local): 0.0.0.0:6000/api/v1
```

### API docs

```uri
https://documenter.getpostman.com/view/13293110/2sAYBSkDbE
```
