# Getting started

> *If you run into any problem, please [post it as an issue on GitHub](https://github.com/matthiasnoback/building-autonomous-services-workshop/issues/new).*

1. For this project you need to have installed on your machine:

    - Docker Engine CE.
    - Docker Compose (install the latest stable version, don't use `apt-get` and the likes).
    - GNU Make (should be available if you're running Linux or Mac, just try `make -v` in a terminal).
    - Bash (same here, just try `bash --version` in a terminal).
    - Git (run `git --version` to see if you already have it installed).
    - A PHP IDE, preferably PhpStorm.
 
2. Clone this project to your machine:

    ```bash
    git clone git@github.com:matthiasnoback/building-autonomous-services-workshop.git
    ```

3. Next, `cd` into the project directory and run:

    ```bash
    make up
    ```

You should finally see a message asking you to open [http://dashboard.localhost](http://dashboard.localhost) in your browser. When you do this, you should see a nice web application. Feel free to click around.
