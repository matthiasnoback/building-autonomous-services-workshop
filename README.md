# Getting started

> *If you run into any problem, please [post it as an issue on GitHub](https://github.com/matthiasnoback/building-autonomous-services-workshop/issues/new).*

1. For this project you need to have installed on your machine:

    - Docker Engine CE.
    - Docker Compose (install the latest stable version, don't use `apt-get` and the likes).
    - Bash (same here, just try `bash --version` in a terminal).
    - Git (run `git --version` to see if you already have it installed).
    - A PHP IDE, preferably PhpStorm.
 
2. Clone this project to your machine:

    ```bash
    git clone git@github.com:matthiasnoback/building-autonomous-services-workshop.git
    ```

3. Next, `cd` into the project directory and run:

    ```bash
    bin/install
    ```

4. Run the application:

   ```bash
   bin/start
   ```

You should finally see a message asking you to open [http://dashboard.localtest.me](http://dashboard.localtest.me) in your browser. When you do this, you should see a nice web application. Feel free to click around.

## Troubleshooting

### Docker says: "Bind for 0.0.0.0:80 failed: port is already allocated"

You have some service running that's already listening on port 80 (like a local Apache or Nginx or something). Close it first, then try again.

## A note for Windows users

This setup should work on Windows too, with Docker for Windows and Git Bash. 

If you feel like you won't be able to install all the tools listed above on your Windows machine, you may want to take a look at [Get started with Docker Machine and a local VM](https://docs.docker.com/machine/get-started/)).
