# JMSJobQueueBundle

Fork from the original [JMSJobQueueBundle](https://github.com/schmittjoh/JMSJobQueueBundle) to support **PHP 8**, **Symfony 6+**, and **Doctrine Common 3**.

---

## Running in Docker

A `Dockerfile` and `docker-compose.yml` are included for local development.

Start a PHP container and open a shell:

```bash
docker compose run --rm php bash
```

This builds and runs the container, allowing you to execute Composer, PHPUnit, Rector, or PHPStan inside it.

---

## Development Tools

### Run Rector

```bash
vendor/bin/rector process src
```

### Run PHPStan

```bash
vendor/bin/phpstan analyse --level=5 --memory-limit=1G
```

### Run Tests

```bash
vendor/bin/phpunit
```

---

## 📖 Documentation

- Bundle docs: [Resources/doc](http://jmsyst.com/bundles/JMSJobQueueBundle)

---

## ⚖️ Licenses

- **Code License:** [Resources/meta/LICENSE](https://github.com/schmittjoh/JMSJobQueueBundle/blob/master/Resources/meta/LICENSE)
- **Documentation License:** [Resources/doc/LICENSE](https://github.com/schmittjoh/JMSJobQueueBundle/blob/master/Resources/doc/LICENSE)


JMSJobQueueBundle
=================

Documentation: 
[Resources/doc](http://jmsyst.com/bundles/JMSJobQueueBundle)
    

Code License:
[Resources/meta/LICENSE](https://github.com/schmittjoh/JMSJobQueueBundle/blob/master/Resources/meta/LICENSE)


Documentation License:
[Resources/doc/LICENSE](https://github.com/schmittjoh/JMSJobQueueBundle/blob/master/Resources/doc/LICENSE)
