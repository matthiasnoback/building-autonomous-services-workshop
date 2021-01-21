# Tips & Tricks

- Run `bin/ps` to look at the status of all the processes involved.
- Run `bin/logs` to take a look at the logs of all the services.
- Run `bin/restart` if you want to start all services, but restart the consumers (since they are long-running processes, code changes won't be taken into account before a restart).
- Run `bin/cleanup` if you want to delete all the data that has been created using `Database::persist()` and `Stream::produce()`.
- Run `bin/cleanup && bin/restart && bin/logs` to restart all consumers with no data available.
- Use `dump()` to conveniently show variables.
