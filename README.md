<div align="center">

# Modulify ✨
<br/>

A simple, intuitive and easy laravel module manager. It allows you to create and delete modules with ease. The modules contain routes, controllers, views and models and also migrations.

[Quick start](#-quick-start) · [Usage](#-usage) · [Todo](#-todo) · [Work in progress](#-work-in-progress) · [License](#-license)
</div>

## 😅 But why would I need modules?

Well, wether you're working on a big project or a small one, you might want to keep your code organized and separated. This package allows you to create modules that contain all the necessary files for a single independent module, where you can use it standalone in another project or implement it in your current project.

## 🫡 Quick start
```bash
composer require xurape/modulify
```

## 🤔 Usage
Create a new module
```bash
php artisan modulify:make <name>
```

Delete a module
```bash
php artisan modulify:delete <name>
```

List all modules
```bash
php artisan modulify:list
```

## 😎 TODO
- [ ] Make a good logo for the package 😂
- [ ] Make a good documentation on how to use the package (idk if it is really necessary, but it would be cool)
- [ ] Make a `modulify:version` to check current version
- [ ] Make a `modulify:search` to search for modules
- [ ] Make a `modulify:doctor` to analyse all the modules and check for errors
- [ ] Make a way to create models, views, migrations and controllers
- [ ] Make a way to list all models, views, migrations and controllers

## 💪 Work in progress
> [!TIP]
> In the future, you will be able to create models, migrations and controllers

## 📝 License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
