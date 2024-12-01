# Setting up Code Coverage Driver

To enable code coverage in your Laravel application, you need to install and configure the Xdebug extension. Here's how to do it:

## 1. Install Xdebug

### For Ubuntu/Debian:
```bash
sudo apt-get install php-xdebug
```

### For macOS with Homebrew:
```bash
pecl install xdebug
```

### For Windows:
Download the appropriate DLL from the [Xdebug website](https://xdebug.org/download) and add it to your PHP extensions.

## 2. Verify Installation

After installing, verify Xdebug is installed:
```bash
php -v
```
You should see Xdebug mentioned in the output.

## 3. Configure Xdebug

Add these settings to your php.ini file:
```ini
[xdebug]
xdebug.mode=coverage
xdebug.start_with_request=yes
```

## 4. Verify Coverage Works

Run PHPUnit with coverage:
```bash
./vendor/bin/phpunit --coverage-html coverage
```

The coverage report will be generated in the `coverage` directory.

## Notes
- Your project already has PHPUnit configured correctly in `phpunit.xml`
- The necessary testing packages are already installed via Composer
- Make sure your PHP version (^8.2 as specified in composer.json) is compatible with the Xdebug version you install