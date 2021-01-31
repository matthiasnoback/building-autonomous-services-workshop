# Tips & Tricks

- Run `bin/ps` to look at the status of all the processes involved.
- Run `bin/logs` to take a look at the logs of all the services (unfortunately, Docker doesn't show the log messages in chronological order, so keep the terminal open to see new log messages appear).
- Run `bin/restart` if you want to start all services, but restart the consumers (since they are long-running processes, code changes won't be taken into account before a restart).
- Run `bin/cleanup` if you want to delete all the data that has been created using `Database::persist()` and `Stream::produce()`.
- Run `bin/crl` as a shortcut for `bin/cleanup && bin/restart && bin/logs`, restart all consumers with no data.
- Use `dump()` to conveniently show variables.
