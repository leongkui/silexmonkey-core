#! /bin/bash

if [ ! -e logs ]; then 
	echo "logs directory does not exist, creating..."
	/bin/mkdir -p logs;
fi

if [ ! -e cache ]; then 
	echo "cache directory does not exist, creating..."
	/bin/mkdir -p cache;
fi