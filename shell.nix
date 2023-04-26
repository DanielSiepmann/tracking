{ pkgs ? import <nixpkgs> { } }:

let
  php = pkgs.php82;
  inherit(pkgs.php82Packages) composer;

  projectInstall = pkgs.writeShellApplication {
    name = "project-install";
    runtimeInputs = [
      php
      composer
    ];
    text = ''
      rm -rf .Build/ vendor/
      composer update --prefer-dist --no-progress --working-dir="$PROJECT_ROOT"
    '';
  };
  projectValidateComposer = pkgs.writeShellApplication {
    name = "project-validate-composer";
    runtimeInputs = [
      php
      composer
    ];
    text = ''
      composer validate
    '';
  };
  projectValidateXml = pkgs.writeShellApplication {
    name = "project-validate-xml";
    runtimeInputs = [
      pkgs.libxml2
      pkgs.wget
      projectInstall
    ];
    text = ''
      project-install
      xmllint --schema vendor/phpunit/phpunit/phpunit.xsd --noout phpunit.xml.dist
      wget --no-check-certificate https://docs.oasis-open.org/xliff/v1.2/os/xliff-core-1.2-strict.xsd --output-document=xliff-core-1.2-strict.xsd
      # shellcheck disable=SC2046
      xmllint --schema xliff-core-1.2-strict.xsd --noout $(find Resources -name '*.xlf')
    '';
  };
  projectTest = pkgs.writeShellApplication {
    name = "project-test";
    runtimeInputs = [
      php
    ];
    text = ''
      ./vendor/bin/phpunit --testdox
    '';
  };
  projectCgl = pkgs.writeShellApplication {
    name = "project-cgl";
    runtimeInputs = [
      php
    ];
    text = ''
      ./vendor/bin/php-cs-fixer fix --dry-run --diff
    '';
  };
  projectCglFix = pkgs.writeShellApplication {
    name = "project-cgl-fix";
    runtimeInputs = [
      php
    ];
    text = ''
      ./vendor/bin/php-cs-fixer fix --diff
    '';
  };

in pkgs.mkShell {
  name = "TYPO3 Extension Watchlist";
  buildInputs = [
    projectInstall
    projectValidateComposer
    projectValidateXml
    projectCgl
    projectCglFix
    projectTest
    php
    composer
  ];

  shellHook = ''
    export PROJECT_ROOT="$(pwd)"

    export typo3DatabaseDriver=pdo_sqlite
  '';
}
