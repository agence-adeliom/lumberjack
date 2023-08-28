# Lumberjack Adeliom Wordpress Starter

## Installation

Dans le fichier `.lando.yml`, changez le name par le nom de votre projet. Ce sera l'URL locale qui sera générée en fonction de ce nom.

```bash
$ lando start
```

### Demande d'authentification lors du composer install

#### Identifiant

licensekey
####password (Clé ACF)
https://codex.adeliom.com/books/wordpress/page/plugins-achet%C3%A9s
Répondre y

#### Identifiant

licensekey
####password (Clé GF)
https://codex.adeliom.com/books/wordpress/page/plugins-achet%C3%A9s

Si le .env et le .env.local n'est pas généré, il faut relancer un composer install :

```bash
$ lando composer install
```

## Infos utiles sur lando

```bash
$ lando start
```

## WP CLI

```bash
$ https://kinsta.com/fr/blog/wp-cli/
```

## Base de données

- Ajouter phpMyAdmin dans le fichier `.lando.yml` dans la partie service

```
  pma:
  type: phpmyadmin
```

- Utiliser le logiciel TablePlus
- Utiliser l'interface de phpMyAdmin

## Mise à jour du monorepo

* Mettre en place les modifications souhaitées sur les packages
* Créer un commit contenant les modifications à déployer
* Exécuter la commande suivante pour déployer les modifications sur les différents repos :

```bash
$ vendor/bin/monorepo-builder release patch
```

_Les modifications peuvent mettre un certain temps (2-3 minutes) avant d'apparaître sur les autres repositories_
