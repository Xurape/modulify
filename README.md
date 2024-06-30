<div align="center">
<img src="https://i.ibb.co/yXxQtsx/logo-original-big-cut.png" width="50%" />

# Modulify
A simple, intuitive and easy laravel module manager. It allows you to create and delete modules with ease. The modules can contain routes, controllers, views and models.

[![Latest Stable Version](https://poser.pugx.org/xurape/modulify/v)](//packagist.org/packages/xurape/modulify) [![Total Downloads](https://poser.pugx.org/xurape/modulify/downloads)](//packagist.org/packages/xurape/modulify) [![Latest Unstable Version](https://poser.pugx.org/xurape/modulify/v/unstable)](//packagist.org/packages/xurape/modulify) [![License](https://poser.pugx.org/xurape/modulify/license)](//packagist.org/packages/xurape/modulify)

[Quick start](#-quick-start) · [Usage](#-usage) · [Todo](#-todo) · [License](#-license)
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

List a module in detail (Controllers, models and migrations)
```bash
php artisan modulify:list --module=<name>
```

Check up modulify with the doctor for any problems 
```bash
php artisan modulify:doctor
```

Get current modulify version
```bash
php artisan modulify:version
```

Update modulify to the latest version! ✨
```bash
php artisan modulify:update
```

## 😎 TODO
- [ ] Create a good documentation on how to use the package
- [ ] Create a `modulify:search` to search for modules
- [] Make a way to list all views
- [ ] Add middleware integration
- [X] Create a `modulify:doctor` to analyse all the modules and check for errors
- [X] Create more unit tests for all commands
- [X] Create a ~good~ logo for the package 😂
- [X] Create a `modulify:update` to update modulify
- [X] Create a `modulify:version` to check current version

## 📝 License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
