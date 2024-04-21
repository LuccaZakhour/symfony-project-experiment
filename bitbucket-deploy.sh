echo "\$ROOT_PATH: $ROOT_PATH"
BUILD_PATH="$ROOT_PATH/build"
echo "\$BUILD_PATH: $BUILD_PATH"
echo "\$RELEASE_PATH: $RELEASE_PATH"
echo "\$PHP: $PHP"

rm -rf "$BUILD_PATH"
mkdir -p "$BUILD_PATH"

echo "unpacking"
echo "$BUILD_PATH_CODE: "
tar xfz "$BUILD_PATH_CODE" -C "$BUILD_PATH"

echo "linking log directory"
rm -rf "$BUILD_PATH/var/log"
mkdir -p "$BUILD_PATH/var"
ln -s "$ROOT_PATH/log" "$BUILD_PATH/var/log" # NOTE, 10.2.2024. Toni, this is used for all environments at the moment, but it should be changed to be environment specific

run_and_echo() {
  command_to_run="$@"
  echo "Running: $command_to_run"
  eval "$command_to_run"
}

echo "current directory: $(pwd)"
echo "moving files"
run_and_echo 'rm -rf "$RELEASE_PATH"'
run_and_echo 'mv "$BUILD_PATH" "$RELEASE_PATH"'

# link global .clients
echo "linking .clients"
run_and_echo 'ln -s "$ROOT_PATH/.clients" $RELEASE_PATH/config/.clients'

#echo "linking files to public directory"
#run_and_echo 'ln -s $ROOT_PATH/files ./public'



# link export files
echo "linking export files"

mkdir -p /usr/home/labowl/public_html/labowl_de_app/bin/export/elab/files
mkdir -p /usr/home/labowl/public_html/files/clientId
rm /usr/home/labowl/public_html/labowl_de_app/bin/export/elab/files/clientId
ln -s /usr/home/labowl/public_html/files/clientId /usr/home/labowl/public_html/labowl_de_app/bin/export/elab/files

mkdir -p /usr/home/labowl/public_html/test/application/bin/export/elab/files
mkdir -p /usr/home/labowl/public_html/files/test/clientId
rm /usr/home/labowl/public_html/test/application/bin/export/elab/files/clientId
ln -s /usr/home/labowl/public_html/files/test/clientId /usr/home/labowl/public_html/test/application/bin/export/elab/files


# Remove the existing directory if it exists
if [ -d "$BUILD_PATH/public/uploads" ]; then
    run_and_echo 'rm -rf "$BUILD_PATH/public/uploads"'
fi

if [ -d "$BUILD_PATH/files" ]; then
    run_and_echo 'rm -rf "$BUILD_PATH/files"'
fi

# echo current directory
echo "current directory: $(pwd)"

# if directory doesn't exist
if [ ! -d "$ROOT_PATH/files/uploads" ]; then
    run_and_echo 'mkdir -p "$ROOT_PATH/files/uploads"'
fi

run_and_echo 'ln -s "$RELEASE_PATH/files/uploads" "$ROOT_PATH/files/public"'

# Check if the clients file does not exist
if [ ! -f "$RELEASE_PATH/config/.clients" ]; then
    # File does not exist, so touch it to create
    touch "$RELEASE_PATH/config/.clients"
    # Then change its permissions
    chmod 777 "$RELEASE_PATH/config/.clients"
fi

sed -i -e 's/DATABASE_URL/#DATABASE_URL/g' $RELEASE_PATH/.env

echo "current directory: $(pwd)"
run_and_echo '$PHP "$RELEASE_PATH/bin/consoleForAllClients" doctrine:migrations:migrate -n'
$PHP "$RELEASE_PATH/bin/consoleForAllClients" assets:install -n

$PHP "$RELEASE_PATH/bin/consoleForAllClients" cache:clear
$PHP "$RELEASE_PATH/bin/consoleForAllClients" cache:clear --env=prod

echo "current directory: $(pwd)"
echo "removing assets"
rm -rf "$BUILD_PATH_CODE"

echo "successfully deployed"

