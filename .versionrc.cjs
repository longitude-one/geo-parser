const config = require('./.versionrc.json');

module.exports = {
  ...config,
  writerOpts: {
    // Add the release version after grouping and sorting, preserving the default renderer.
    finalizeContext(context) {
      const suffix = ' — ' + context.version;
      const versionedGroups = (groups) => groups?.map((group) => ({
        ...group,
        title: group.title ? group.title + suffix : group.title,
      }));

      return {
        ...context,
        commitGroups: versionedGroups(context.commitGroups),
        // Preserve note icons before changing the titles used by the renderer.
        noteGroups: context.noteGroups?.map((group) => ({
          ...group,
          title: [group.title === 'BREAKING CHANGES' ? '⚠' : '', group.title + suffix]
            .filter(Boolean).join(' '),
        })),
      };
    },
  },
};
