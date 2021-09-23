CoreAdmin API
=============
| see the `official CoreAdmin API reference <https://solr.apache.org/guide/coreadmin-api.html>`_ for the api's official documentation.
| currently supported features and commands:

console commands
----------------
| in order to use these commands, make sure you've got at least one :doc:`endpoint <../reference/configuration/endpoints>` configured.


status
~~~~~~
| the `status <https://solr.apache.org/guide/coreadmin-api.html#coreadmin-status>`_ command is available by running

.. code-block:: bash

    $ php bin/console solr:core:status

| **options**:

 * ``--core``: will show status for specific core
 * ``--omit-index-info``: will omit index info

create
~~~~~~
| the `create <https://solr.apache.org/guide/coreadmin-api.html#coreadmin-create>`_ command is available by running

.. code-block:: bash

    $ php bin/console solr:core:create <core-name>

| **options**:

 * ``--instance-dir``: directory where files for this core should be stored. default is the value specified for the name parameter if not supplied
 * ``--config``: name of the config file (i.e. solrconfig.xml) relative to instance-dir
 * ``--schema``: name of the schema file to use for the core.
 * ``--data-dir``: name of the data directory relative to instance-dir.
 * ``--config-set``: name of the configset to use for this core.
 * ``--collection``: name of the collection to which this core belongs. default is the name of the core
 * ``--shard``: shard id this core represents
 * ``--async``: request id to track this action which will be processed asynchronously


reload
~~~~~~
| the `reload <https://solr.apache.org/guide/coreadmin-api.html#coreadmin-reload>`_ command is available by running

.. code-block:: bash

    $ php bin/console solr:core:reload <core-name>

rename
~~~~~~
| the `rename <https://solr.apache.org/guide/coreadmin-api.html#coreadmin-rename>`_ command is available by running

.. code-block:: bash

    $ php bin/console solr:core:rename <core-name> <new-name>

| **options**:

 * ``--async``: request id to track this action which will be processed asynchronously

swap
~~~~
| the `swap <https://solr.apache.org/guide/coreadmin-api.html#coreadmin-swap>`_ command is available by running

.. code-block:: bash

    $ php bin/console solr:core:swap <core-name> <new-name>

| **options**:

 * ``--async``: request id to track this action which will be processed asynchronously

unload
~~~~~~
| the `unload <https://solr.apache.org/guide/coreadmin-api.html#coreadmin-unload>`_ command is available by running

.. code-block:: bash

    $ php bin/console solr:core:reload <core-name>

| **options**:

 * ``--delete-index``: will remove the index when unloading the core
 * ``--delete-data-dir``: removes the data directory and all sub-directories
 * ``--delete-instance-dir``: removes everything related to the core, including the index directory, configuration files and other related files
 * ``--async=<value>``: request id to track this action which will be processed asynchronously

merge-indexes
~~~~~~~~~~~~~
| the `merge indexes <https://solr.apache.org/guide/coreadmin-api.html#coreadmin-mergeindexes>`_ command is available by running

.. code-block:: bash

    $ php bin/console solr:core:merge-indexes <core-name>

| **options**:

 * ``--index-dir``: multi-valued, directories that would be merged
 * ``--src-core``: multi-valued, source cores that would be merged
 * ``--async``: request id to track this action which will be processed asynchronously

split
~~~~~
| the `split <https://solr.apache.org/guide/coreadmin-api.html#coreadmin-split>`_ command is available by running

.. code-block:: bash

    $ php bin/console solr:core:split <core-name>

| **options**:

 * ``--path``: multi-valued, the directory path in which a piece of the index will be writtenmulti-valued, the directory path in which a piece of the index will be written
 * ``--target-core``: multi-valued, the target solr core to which a piece of the index will be mergedmulti-valued, the target solr core to which a piece of the index will be merged
 * ``--ranges``: comma-separated list of hash ranges in hexadecimal format
 * ``--split-key``: key to be used for splitting the index
 * ``--async``: request id to track this action which will be processed asynchronously