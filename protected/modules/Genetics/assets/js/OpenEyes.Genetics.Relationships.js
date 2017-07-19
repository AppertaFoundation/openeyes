this.OpenEyes = this.OpenEyes || {};
this.OpenEyes.Genetics = this.OpenEyes.Genetics || {};

(function (exports) {
  var template = '<li><span class="genetics_relationship_remove"> <i class="fa fa-minus-circle" title="Remove Relationship"></i></span> ' +
      '<input type="hidden" name="GeneticsPatient[relationships][{{relatedId}}][related_to_id]" value="{{relatedId}}">' +
      '{{name}} is a ' +
      '<select id="relationship_id" name="GeneticsPatient[relationships][{{relatedId}}][relationship_id]">' +
      '{{#relationships}} <option value="{{id}}" >{{name}}</option>{{/relationships}}</select>' +
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
    $('#relationships_list').on('click', '.genetics_relationship_remove', Relationships.remove);
  };

  Relationships.newRelationshipForm = function(item) {
    return Mustache.render(template, {
      name: item['first_name'] + ' ' + item['last_name'],
      relatedId: item.genetics_patient_id,
      relationships: possibleRelationships
    });
  };

  exports.Relationships = Relationships;
}(this.OpenEyes.Genetics));