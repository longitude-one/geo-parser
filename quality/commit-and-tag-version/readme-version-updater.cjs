/**
 * Custom updater for commit-and-tag-version to update the README installation example and documentation version.
 */

const SEMVER_PATTERN = '\\d+\\.\\d+\\.\\d+(?:-[0-9A-Za-z.-]+)?(?:\\+[0-9A-Za-z.-]+)?';
const PACKAGE_VERSION_PATTERN = new RegExp(
  `(composer require longitude-one/geo-parser:)\\^?(${SEMVER_PATTERN})(?=\\s|$)`,
);

module.exports.readVersion = function (contents) {
  const match = contents.match(PACKAGE_VERSION_PATTERN);

  if (!match) {
    throw new Error('readme-version-updater: No Geo Parser installation version found in README.md.');
  }

  return match[2];
};

module.exports.writeVersion = function (contents, version) {
  module.exports.readVersion(contents);

  const major = version.split('.')[0];

  return contents.replace(
    new RegExp(PACKAGE_VERSION_PATTERN.source, 'g'),
    (_, prefix) => `${prefix}^${version}`,
  ).replace(
    /(https:\/\/img\.shields\.io\/badge\/docs-v)\d+(-blue)/g,
    (_, prefix, suffix) => `${prefix}${major}${suffix}`,
  ).replace(
    /(https:\/\/www\.longitude-one\.fr\/geo-parser\/v)\d+(\/index\.html)/g,
    (_, prefix, suffix) => `${prefix}${major}${suffix}`,
  );
};
