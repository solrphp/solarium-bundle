#!/bin/bash
set -euo pipefail

if [[ "${VERBOSE:-}" == "yes" ]]; then
    set -x
fi

. /opt/docker-solr/scripts/run-initdb

echo $SOLR_HOST

echo "creating demo collection";
/opt/solr/bin/solr create -c demo -p 8983;
echo "demo collection created";

echo "posting data";
/opt/solr/bin/post -host $SOLR_HOST -c demo -commit no example/exampledocs/*.xml;
/opt/solr/bin/post -host $SOLR_HOST -c demo -commit no example/exampledocs/books.json;
/opt/solr/bin/post -host $SOLR_HOST -c demo -commit yes example/exampledocs/books.csv;
echo "done"

stop-local-solr
exec solr-fg