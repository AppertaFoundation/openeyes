/**
 * NotificationDemo.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

// tslint:disable:no-console

import { Arr } from '@ephox/katamari';

declare let tinymce: any;

export default function () {
  const notifyShort = function (type) {
    const notification = tinymce.activeEditor.notificationManager.open({
      type,
      text: 'This is an example ' + (type ? type : 'blank') + ' message.'
    });

    setTimeout(function () {
      notification.text('Message changed.');
    }, 5000);
    console.log(notification);
  };

  const notifyLong = function (len) {
    const longTextMessage = [];

    for (let i = 0; i < len; i++) {
      longTextMessage.push('bla');
    }

    const notification = tinymce.activeEditor.notificationManager.open({
      text: longTextMessage.join(' ')
    });
    console.log(notification);
  };

  const notifyExtraLong = function (len) {
    const longTextMessage = ['this is text '];

    for (let i = 0; i < len; i++) {
      longTextMessage.push('bla');
    }

    const notification = tinymce.activeEditor.notificationManager.open({
      text: longTextMessage.join('')
    });
    console.log(notification);
  };

  const notifyProgress = function (percent) {
    const notification = tinymce.activeEditor.notificationManager.open({
      text: 'Progress text',
      progressBar: true
    });

    notification.progressBar.value(percent);

    setTimeout(function () {
      notification.progressBar.value(90);
    }, 5000);
    console.log(notification);
  };

  const notifyTimeout = function (time) {
    const notification = tinymce.activeEditor.notificationManager.open({
      text: 'Timeout: ' + time,
      timeout: time
    });
    console.log(notification);
  };

  const notifyIcon = function () {
    const notification = tinymce.activeEditor.notificationManager.open({
      text: 'Text',
      icon: 'bold'
    });
    console.log(notification);
  };

  Arr.each([
    { title: 'success', action: notifyShort, value: 'success' },
    { title: 'error', action: notifyShort, value: 'error' },
    { title: 'warn', action: notifyShort, value: 'warning' },
    { title: 'info', action: notifyShort, value: 'info' },
    { title: 'blank', action: notifyShort },
    { title: 'notifyLong', action: notifyLong, value: 100 },
    { title: 'notifyExtraLong', action: notifyExtraLong, value: 100 },
    { title: 'notifyProgress', action: notifyProgress, value: 50 },
    { title: 'notifyTimeout', action: notifyTimeout, value: 3000 },
    { title: 'notifyIcon', action: notifyIcon }
  ], function (notification) {
    const btn = document.createElement('button');
    btn.innerHTML = notification.title;
    btn.onclick = function () {
      notification.action(notification.value);
    };
    document.querySelector('#ephox-ui').appendChild(btn);
  });

  tinymce.init({
    selector: 'textarea.tinymce',
    skin_url: '../../../../../js/tinymce/skins/lightgray',
    codesample_content_css: '../../../../../js/tinymce/plugins/codesample/css/prism.css'
  });

  tinymce.init({
    selector: 'div.tinymce',
    inline: true,
    skin_url: '../../../../../js/tinymce/skins/lightgray',
    codesample_content_css: '../../../../../js/tinymce/plugins/codesample/css/prism.css'
  });
}