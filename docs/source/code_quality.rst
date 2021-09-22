code quality
============

testing
-------

| this bundle assures code quality using the following methods

* `php-cs-fixer <https://github.com/FriendsOfPHP/PHP-CS-Fixer>`_ : ``composer run-phpcsf``
* `symfony coding standard <https://github.com/djoos/Symfony-coding-standard>`_ : ``composer run-phpcs``
* `phpstan <https://github.com/phpstan/phpstan>`_ (level 8) : ``composer run-phpstan``
* `phpunit <https://github.com/sebastianbergmann/phpunit>`_:  ``composer run-phpunit`` (no coverage) or  ``composer run-phpunitc`` (including coverage)
* `infection <https://github.com/infection/infection>`_ mutation testing (MSI >= 98%): ``composer run-infection``

.. note::
    ``$ composer run-infection`` uses the `threads option <https://infection.github.io/guide/command-line-options.html#threads-or-j>`_ for mac os

documentation
-------------
| to build this bundle's documentation run:

.. code-block:: bash

    $ composer run-sphinx

| and find the result in the ``./build/docs`` directory

metrics
-------
| to view this bundle's metrics run:

.. code-block:: bash

    $ composer run-phpmetrics

| and find the result in the ``./build/phpmetrics`` directory

.. tip::
    | for convenience the ``composer run-all`` script executes all aforementioned commands;
    | one can use this as a git pre-commit hook if you'd like to contribute.