/**
 * Custom updater for `commit-and-tag-version` to update README.md.
 *
 * The built-in `plain-text` type replaces the entire file with the version number,
 * which is unusable for a README. This updater only replaces the version number
 * in the "Version: x.y.z" mention (first occurrence) and leaves the rest of the file intact.
 */

const fs = require('node:fs');

// Capture "Version: " (group 1) followed by a semver, optionally with a
// pre-release or build metadata (group 2).
const VERSION_PATTERN = /\s*(\d+\.\d+\.\d+(?:[-+][0-9A-Za-z-.]+)?)/;

module.exports.readVersion = function (contents) {
  const match = contents.match(VERSION_PATTERN);
  return match ? match[1] : '0.0.0';
};

module.exports.writeVersion = function (contents, version) {
  if (!VERSION_PATTERN.test(contents)) {
    throw new Error(
      "readme-version-updater: No 'x.y.z' mention found in README.md. " +
        "The version cannot be updated.",
    );
  }
  const updatedContents = contents.replace(VERSION_PATTERN, `${version}`);

  // Log the updated file contents (equivalent to `cat`)
  console.log("\n--- Updated README.md ---");
  console.log(updatedContents);
  console.log("--- End of file ---\n");

  return updatedContents;
};
