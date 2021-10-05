getting started
===============

| a big part of this bundle's functionality is about managing your solr instance from within your application / the command line.
| knowing solr config files can be huge and you might have to multiply that for each of your cores, a config dump command is provided.

| this command requests the schema.xml from the given core, parses it and outputs the result in a config file (yaml and php only for now).
| from there you can copy & paste / re-format / etc. and use the contents for your ``solrphp_solarium`` config node

.. _config-generate-label:

config generate command
-----------------------
| to dump a dummy config file from your schema.xml and solrconfig.xml run the command below.
| by default it dumps all the manageable nodes. if you want to filter out one or more nodes, use the provided options.

.. code-block:: bash

    $ php bin/console solr:config:generate <core-name> <format>

| **options**:

* ``--exclude-fields``: do not dump fields configuration
* ``--exclude-copy-fields``: do not dump copy fields configuration
* ``--exclude-dynamic-fields``: do not dump dynamic fields configuration
* ``--exclude-fields-types``: do not dump field types configuration
* ``--exclude-update-handler``: do not dump update handler configuration
* ``--exclude-query``: do not dump query configuration
* ``--exclude-request-dispatcher``: do not dump request dispatcher configuration
* ``--exclude-request-handlers``: do not dump request handler configuration
* ``--exclude-search-components``: do not dump search component configuration

data collector
--------------
| this bundle comes with a data collector to ease debugging of your solr requests.

.. note::

    | if you have a custom ``dispatcher_service`` or ``dispatcher_class`` defined in your :doc:`client config <../reference/configuration/clients>`, the data collector will only work if its an instance of ``Symfony\Component\EventDispatcher\EventDispatcherInterface``

