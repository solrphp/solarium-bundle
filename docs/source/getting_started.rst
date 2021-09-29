getting started
===============

| a big part of this bundle's functionality is about managing your solr instance from within your application / the command line.
| knowing solr config files can be huge and you might have to multiply that for each of your cores, a config dump command is provided.

| this command requests the schema.xml from the given core, parses it and outputs the result in a config file (yaml and php only for now).
| from there you can copy & paste / re-format / etc. and use the contents for your ``solrphp_solarium`` config node

usage
-----
| to dump a config file from your schema.xml run the command below.
| by default it dumps all the manageable schema nodes. if you want to filter out one or more nodes, use the provided options

.. code-block:: bash

    $ php bin/console solr:config:generate <core-name> <format>

| **options**:

* ``--exclude-fields``: do not dump fields configuration
* ``--exclude-copy-fields``: do not dump copy fields configuration
* ``--exclude-dynamic-fields``: do not dump dynamic fields configuration
* ``--exclude-fields-types``: do not dump field types configuration
