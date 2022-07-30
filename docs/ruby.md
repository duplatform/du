```
            ___   _____   _         _______  ______  ____   _____   __  __ 
     /\    |__ \ |  __ \ | |    /\ |__   __||  ____|/ __ \ |  __ \ |  \/  |
    /  \      ) || |__) || |   /  \   | |   | |__  | |  | || |__) || \  / |
   / /\ \    / / |  ___/ | |  / /\ \  | |   |  __| | |  | ||  _  / | |\/| |
  / ____ \  / /_ | |     | | / ____ \ | |   | |    | |__| || | \ \ | |  | |
 /_/    \_\|____||_|     |_|/_/    \_\|_|   |_|     \____/ |_|  \_\|_|  |_|
                                                                       By Jubayed
                                                                           
```

# Install
```sh
composer global require jubayed/a2:dev-main
```

create a directory
```sh
mkdir -p test-item/_html
```

past all html files into `test-item/_html`

```sh
cd test-item
```

Run a2 development server
```sh
a2 s
```

Set a2 token
```env
a2 s --token your_a2token
```


## add fedora 

`cat > /home/jubayed/.composer/composer.json` 

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:jubayed/a2-cli.git"
        }
    ],
    "require": {
        "jubayed/a2": "dev-main"
    }
}
```
