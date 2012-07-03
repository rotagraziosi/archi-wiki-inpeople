#!/bin/sh
xgettext -j ../../../modules/header/templates/header.tpl --language=PHP --from-code=UTF-8 --package-name=ArchiWiki --package-version=2.3
xgettext -j ../../../modules/archi/templates/*.tpl --language=PHP --from-code=UTF-8 --package-name=ArchiWiki --package-version=2.3
xgettext -j ../../../modules/archi/includes/*.php --language=PHP --from-code=UTF-8 --package-name=ArchiWiki --package-version=2.3
xgettext -j ../../../includes/framework/config.class.php --language=PHP --from-code=UTF-8 --package-name=ArchiWiki --package-version=2.3
xgettext -j ../../../includes/framework/frameworkClasses/*.php --language=PHP --from-code=UTF-8 --package-name=ArchiWiki --package-version=2.3
xgettext -j ../../../modules/*/*.php --language=PHP --from-code=UTF-8 --package-name=ArchiWiki --package-version=2.3
