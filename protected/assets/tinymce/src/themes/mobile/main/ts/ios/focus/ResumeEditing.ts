import { Compare, Element, Focus } from '@ephox/sugar';

import CursorRefresh from '../../touch/focus/CursorRefresh';

const resume = function (cWin, frame) {
  Focus.active().each(function (active) {
    // INVESTIGATE: This predicate may not be required. The purpose of it is to ensure
    // that the content window's frame element is not unnecessarily blurred before giving
    // it focus.
    if (! Compare.eq(active, frame)) {
      Focus.blur(active);
    }
  });
  // Required when transferring from another input area.
  cWin.focus();

  Focus.focus(Element.fromDom(cWin.document.body));
  CursorRefresh.refresh(cWin);
};

export default {
  resume
};