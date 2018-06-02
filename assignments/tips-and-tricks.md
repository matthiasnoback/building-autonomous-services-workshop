# Tips & Tricks

- Run `make ps` to look at the status of all the processes involved.
- Run `make logs` to take a look at the logs of all the services.
- Run `make up` if you want to start all the services.
- Run `make restart` if you want to start all services, but restart the consumers (since they are long-running processes, code changes won't be taken into account before a restart).
- Use `dump()` to conveniently show variables.
