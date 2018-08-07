/**
 * ImportCss.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

import DOMUtils from 'tinymce/core/api/dom/DOMUtils';
import EditorManager from 'tinymce/core/api/EditorManager';
import Env from 'tinymce/core/api/Env';
import Tools from 'tinymce/core/api/util/Tools';
import Settings from '../api/Settings';
import { Editor } from 'tinymce/core/api/Editor';

const removeCacheSuffix = function (url: string) {
  const cacheSuffix = Env.cacheSuffix;

  if (typeof url === 'string') {
    url = url.replace('?' + cacheSuffix, '').replace('&' + cacheSuffix, '');
  }

  return url;
};

const isSkinContentCss = function (editor: Editor, href: string) {
  const settings = editor.settings, skin = settings.skin !== false ? settings.skin || 'lightgray' : false;

  if (skin) {
    const skinUrl = settings.skin_url ? editor.documentBaseURI.toAbsolute(settings.skin_url) : EditorManager.baseURL + '/skins/' + skin;
    return href === skinUrl + '/content' + (editor.inline ? '.inline' : '') + '.min.css';
  }

  return false;
};

const compileFilter = function (filter: string | RegExp | Function) {
  if (typeof filter === 'string') {
    return function (value) {
      return value.indexOf(filter) !== -1;
    };
  } else if (filter instanceof RegExp) {
    return function (value) {
      return filter.test(value);
    };
  }

  return filter;
};

const getSelectors = function (editor, doc, fileFilter) {
  const selectors = [], contentCSSUrls = {};

  function append(styleSheet, imported?) {
    let href = styleSheet.href, rules;

    href = removeCacheSuffix(href);

    if (!href || !fileFilter(href, imported) || isSkinContentCss(editor, href)) {
      return;
    }

    Tools.each(styleSheet.imports, function (styleSheet) {
      append(styleSheet, true);
    });

    try {
      rules = styleSheet.cssRules || styleSheet.rules;
    } catch (e) {
      // Firefox fails on rules to remote domain for example:
      // @import url(//fonts.googleapis.com/css?family=Pathway+Gothic+One);
    }

    Tools.each(rules, function (cssRule) {
      if (cssRule.styleSheet) {
        append(cssRule.styleSheet, true);
      } else if (cssRule.selectorText) {
        Tools.each(cssRule.selectorText.split(','), function (selector) {
          selectors.push(Tools.trim(selector));
        });
      }
    });
  }

  Tools.each(editor.contentCSS, function (url) {
    contentCSSUrls[url] = true;
  });

  if (!fileFilter) {
    fileFilter = function (href: string, imported: string) {
      return imported || contentCSSUrls[href];
    };
  }

  try {
    Tools.each(doc.styleSheets, function (styleSheet: string) {
      append(styleSheet);
    });
  } catch (e) {
    // Ignore
  }

  return selectors;
};

const defaultConvertSelectorToFormat = function (editor: Editor, selectorText: string) {
  let format;

  // Parse simple element.class1, .class1
  const selector = /^(?:([a-z0-9\-_]+))?(\.[a-z0-9_\-\.]+)$/i.exec(selectorText);
  if (!selector) {
    return;
  }

  const elementName = selector[1];
  const classes = selector[2].substr(1).split('.').join(' ');
  const inlineSelectorElements = Tools.makeMap('a,img');

  // element.class - Produce block formats
  if (selector[1]) {
    format = {
      title: selectorText
    };

    if (editor.schema.getTextBlockElements()[elementName]) {
      // Text block format ex: h1.class1
      format.block = elementName;
    } else if (editor.schema.getBlockElements()[elementName] || inlineSelectorElements[elementName.toLowerCase()]) {
      // Block elements such as table.class and special inline elements such as a.class or img.class
      format.selector = elementName;
    } else {
      // Inline format strong.class1
      format.inline = elementName;
    }
  } else if (selector[2]) {
    // .class - Produce inline span with classes
    format = {
      inline: 'span',
      title: selectorText.substr(1),
      classes
    };
  }

  // Append to or override class attribute
  if (Settings.shouldMergeClasses(editor) !== false) {
    format.classes = classes;
  } else {
    format.attributes = { class: classes };
  }

  return format;
};

const getGroupsBySelector = function (groups, selector: string) {
  return Tools.grep(groups, function (group) {
    return !group.filter || group.filter(selector);
  });
};

const compileUserDefinedGroups = function (groups) {
  return Tools.map(groups, function (group) {
    return Tools.extend({}, group, {
      original: group,
      selectors: {},
      filter: compileFilter(group.filter),
      item: {
        text: group.title,
        menu: []
      }
    });
  });
};

interface StyleGroup {
  title: string;
  selectors: Record<string, any>;
  filter: string | RegExp | Function;
}

const isExclusiveMode = function (editor: Editor, group: StyleGroup) {
  // Exclusive mode can only be disabled when there are groups allowing the same style to be present in multiple groups
  return group === null || Settings.shouldImportExclusive(editor) !== false;
};

const isUniqueSelector = function (editor: Editor, selector: string, group: StyleGroup, globallyUniqueSelectors: Record<string, any>) {
  return !(isExclusiveMode(editor, group) ? selector in globallyUniqueSelectors : selector in group.selectors);
};

const markUniqueSelector = function (editor: Editor, selector: string, group: StyleGroup, globallyUniqueSelectors: Record<string, any>) {
  if (isExclusiveMode(editor, group)) {
    globallyUniqueSelectors[selector] = true;
  } else {
    group.selectors[selector] = true;
  }
};

const convertSelectorToFormat = function (editor, plugin, selector, group) {
  let selectorConverter;

  if (group && group.selector_converter) {
    selectorConverter = group.selector_converter;
  } else if (Settings.getSelectorConverter(editor)) {
    selectorConverter = Settings.getSelectorConverter(editor);
  } else {
    selectorConverter = function () {
      return defaultConvertSelectorToFormat(editor, selector);
    };
  }

  return selectorConverter.call(plugin, selector, group);
};

const setup = function (editor: Editor) {
  editor.on('renderFormatsMenu', function (e) {
    const globallyUniqueSelectors = {};
    const selectorFilter = compileFilter(Settings.getSelectorFilter(editor)), ctrl = e.control;
    const groups = compileUserDefinedGroups(Settings.getCssGroups(editor));

    const processSelector = function (selector: string, group: StyleGroup) {
      if (isUniqueSelector(editor, selector, group, globallyUniqueSelectors)) {
        markUniqueSelector(editor, selector, group, globallyUniqueSelectors);

        const format = convertSelectorToFormat(editor, editor.plugins.importcss, selector, group);
        if (format) {
          const formatName = format.name || DOMUtils.DOM.uniqueId();
          editor.formatter.register(formatName, format);

          return Tools.extend({}, ctrl.settings.itemDefaults, {
            text: format.title,
            format: formatName
          });
        }
      }

      return null;
    };

    if (!Settings.shouldAppend(editor)) {
      ctrl.items().remove();
    }

    Tools.each(getSelectors(editor, e.doc || editor.getDoc(), compileFilter(Settings.getFileFilter(editor))), function (selector: string) {
      if (selector.indexOf('.mce-') === -1) {
        if (!selectorFilter || selectorFilter(selector)) {
          const selectorGroups = getGroupsBySelector(groups, selector);

          if (selectorGroups.length > 0) {
            Tools.each(selectorGroups, function (group) {
              const menuItem = processSelector(selector, group);
              if (menuItem) {
                group.item.menu.push(menuItem);
              }
            });
          } else {
            const menuItem = processSelector(selector, null);
            if (menuItem) {
              ctrl.add(menuItem);
            }
          }
        }
      }
    });

    Tools.each(groups, function (group) {
      if (group.item.menu.length > 0) {
        ctrl.add(group.item);
      }
    });

    e.control.renderNew();
  });
};

export default {
  defaultConvertSelectorToFormat,
  setup
};