function appendText(selector, appendToItem) {
	var textValue = selector[0].options[selector[0].selectedIndex].text;
	// ignore the empty selector
	if ('-' != textValue && '' != textValue) {
		// grab the text
		appendValue = appendToItem.val();
		// if we're adding onto existing text, add a comma
		if (appendValue && '' != appendValue) {
			textValue = appendValue + ', ' + textValue;
		// otherwise just make sure the first letter is capitalized
		} else {
			textValue = textValue.charAt(0).toUpperCase() + textValue.slice(1);
		}
		// add it to the textarea
		appendToItem.val(textValue);
	}
}