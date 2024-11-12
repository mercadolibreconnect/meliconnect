# - How to create .pot files for Meliconnect plugin 
Use wp CLI command to create .pot files for Meliconnect plugin
'''
wp i18n make-pot ./wp-content/plugins/meliconnect ./wp-content/plugins/meliconnect/languages/meliconnect.pot --exclude=node_modules,vendor
'''

if you are in plugin folder in terminal. Run this command
'''
wp i18n make-pot ./ ./languages/meliconnect.pot --exclude=node_modules,vendor
'''






