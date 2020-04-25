export interface Entity {
  id: string;
  model: string;
  properties: object;
  view: string;
  parent_entity_id: string;
  is_active: boolean;
  published_at: string;
  unpublished_at: string;
  version: number;
  entityContents: Array<object>;
  entitiesRelated: Array<object>;
}
