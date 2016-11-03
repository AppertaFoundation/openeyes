this.OpenEyes = this.OpenEyes || {};
this.OpenEyes.Genetics = this.OpenEyes.Genetics || {};

(function (exports) {
  var template = '<li><input type="hidden" name="GeneticsPatient[relationships][{{relatedId}}][related_to_id]" value="{{relatedId}}">' +
      '{{name}} is a ' +
      '<select id="relationship_id" name="GeneticsPatient[relationships][{{relatedId}}][relationship_id]">' +
      '{{#relationships}} <option value="{{id}}" >{{name}}</option>{{/relationships}}</select> ' +
      'to the patient</li>',
    Relationships = {},
    possibleRelationships,
    existingRelationships;

  Relationships.remove = function(event) {
    $(event.target).closest('li').remove();
  };

  Relationships.init = function (relationsList, existingList) {
    possibleRelationships = relationsList;
    existingRelationships = existingList;
    $('.genetics_relationship_remove').on('click', Relationships.remove);
  };

  Relationships.newRelationshipForm = function(item) {
    return Mustache.render(template, {
      name: item['patient.fullName'],
      relatedId: item.id,
      relationships: possibleRelationships
    });
  };

  exports.Relationships = Relationships;
}(this.OpenEyes.Genetics));